               <select name="records-limit" id="records-limit" class="custom-select">
                    <option disabled selected>Nº Linhas</option>
                    <?php foreach([10,20,30,40,50] as $limit) : ?>
                    <option
                        <?php if(isset($_SESSION['records-limit']) && $_SESSION['records-limit'] == $limit) echo 'selected'; ?>
                        value="<?= $limit; ?>">
                        <?= $limit; ?>
                    </option>
                    <?php endforeach; ?>
                </select>