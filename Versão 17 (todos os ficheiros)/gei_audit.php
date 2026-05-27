<?php
/**
 * GEI — Helper central de auditoria
 *
 * USO:
 *   require_once 'gei_audit.php';
 *
 *   // Login bem-sucedido (em validauser.php / verificar_2fa.php)
 *   gei_audit($db, 'login_ok', 'sessao', null, 'Login bem-sucedido', $row['id'], $row['nome'], $row['email']);
 *
 *   // Login falhado (utilizador não autenticado — sem SESSION)
 *   gei_audit($db, 'login_falhou', 'sessao', null, "Email: $rawEmail");
 *
 *   // Logout
 *   gei_audit($db, 'logout', 'sessao');
 *
 *   // Criar registo
 *   gei_audit($db, 'criar', 'equipamento', $novo_id, $nome_equipamento);
 *
 *   // Editar registo
 *   gei_audit($db, 'editar', 'sala', $id_sala, "Nome: $nome");
 *
 *   // Eliminar registo
 *   gei_audit($db, 'eliminar', 'avaria', $id_avaria);
 *
 *   // Exportação
 *   gei_audit($db, 'exportar', 'equipamentos', null, 'PDF exportado');
 *
 *   // Alteração de configuração
 *   gei_audit($db, 'config', 'settings', null, "Campo: $campo");
 *
 * PARÂMETROS:
 *   $db          — ligação mysqli ativa
 *   $acao        — string: login_ok | login_falhou | logout | criar | editar | eliminar | exportar | config
 *   $entidade    — string: nome da entidade (equipamento, sala, avaria, utilizador, …)
 *   $entidade_id — int|null: ID do registo afetado (null se não aplicável)
 *   $detalhe     — string|null: informação adicional livre
 *   $user_id     — int|null: forçar user_id (útil antes de SESSION estar completa, ex: login)
 *   $user_nome   — string|null: forçar nome (idem)
 *   $user_email  — string|null: forçar email (idem)
 */

if (!function_exists('gei_audit')) {

    function gei_audit(
        mysqli  $db,
        string  $acao,
        string  $entidade    = '',
        ?int    $entidade_id = null,
        ?string $detalhe     = null,
        ?int    $user_id     = null,
        ?string $user_nome   = null,
        ?string $user_email  = null
    ): void {

        // ── Resolver utilizador ───────────────────────────────────────────────
        $uid    = $user_id    ?? (isset($_SESSION['user_id'])  ? (int)$_SESSION['user_id']  : null);
        $unome  = $user_nome  ?? ($_SESSION['login_user']      ?? '');
        $uemail = $user_email ?? ($_SESSION['email']           ?? '');

        // ── Resolver IP (suporta proxy / load balancer) ───────────────────────
        $ip = '';
        foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'] as $h) {
            if (!empty($_SERVER[$h])) {
                $ip = trim(explode(',', $_SERVER[$h])[0]);
                break;
            }
        }
        $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : ($_SERVER['REMOTE_ADDR'] ?? '');

        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        // ── Truncar strings para caber nas colunas ────────────────────────────
        $acao     = substr($acao,     0, 60);
        $entidade = substr($entidade, 0, 80);
        $unome    = substr($unome,    0, 120);
        $uemail   = substr($uemail,   0, 180);

        // ── Inserir registo ───────────────────────────────────────────────────
        try {
            $stmt = $db->prepare("
                INSERT INTO auditoria
                    (user_id, user_nome, user_email, acao, entidade, entidade_id, detalhe, ip, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'issssisss',
                $uid, $unome, $uemail,
                $acao, $entidade, $entidade_id,
                $detalhe, $ip, $ua
            );
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            // Nunca bloquear a aplicação por falha de auditoria
            error_log('[GEI audit] ' . $e->getMessage());
        }

        // ── Limpeza automática (probabilidade 1%) ─────────────────────────────
        // Corre em média 1 vez a cada 100 inserções — impacto nulo na performance
        if (rand(1, 100) === 1) {
            gei_audit_purge($db);
        }
    }
}

if (!function_exists('gei_audit_purge')) {

    /**
     * Remove registos de auditoria com mais de $dias dias.
     * Chamada automaticamente pelo gei_audit() com probabilidade de 1%.
     * Pode também ser chamada manualmente: gei_audit_purge($db, 180);
     */
    function gei_audit_purge(mysqli $db, int $dias = 90): void
    {
        try {
            $stmt = $db->prepare("
                DELETE FROM auditoria
                WHERE timestamp < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->bind_param('i', $dias);
            $stmt->execute();
            $apagados = $stmt->affected_rows;
            $stmt->close();

            if ($apagados > 0) {
                error_log("[GEI audit] Purga automática: $apagados registo(s) eliminado(s) (>{$dias} dias)");
            }
        } catch (Throwable $e) {
            error_log('[GEI audit purge] ' . $e->getMessage());
        }
    }
}
