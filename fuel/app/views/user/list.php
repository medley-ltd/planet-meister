<html>
    <meta charset="UTF-8">
    <body>
        <?php foreach ($rows as $row): ?>
            <div style="background-color: #999">
                nico-id<?php echo $row['nico_id']; ?>
            </div>    
            <div>
                <?php echo $row['name']; ?>
            </div>    
            <div>
                <?php echo nl2br($row['level']); ?>
            </div>    
        <?php endforeach; ?>
    </body>
</html>