<?php
/*
## File: setup_database.php
## File Created: Saturday, 11th February 2023 9:44:25 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config Database</title>
        <style>
            .center {
                margin: auto;  
                padding: 50px;
            }
        </style>
</head>
<body>
<div class="center">
            <?php echo form_open('Home/config'); ?>
            <table align=center> 
                <tr>
                    <td>Filename</td>
                    <td>:</td>
                    <td><input name="filename" required="" size="30"></td>
                </tr>
                <tr>
                    <td>Hostname</td>
                    <td>:</td>
                    <td><input name="hostname" required="" size="30"></td>
                </tr>
                <tr>
                    <td>Port</td>
                    <td>:</td>
                    <td><input name="port" required="" size="30"></td>
                </tr>
                <tr>
                    <td>Database</td>
                    <td>:</td>
                    <td><input name="database" required="" size="30"></td>
                </tr>

                <tr>
                    <td>User Name</td>
                    <td>:</td>
                    <td><input name="username" required="" size="30"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td>:</td>
                    <td><input name="password" required="" size="30" type="password"></td>
                </tr>
                <tr>
                    <td>Key</td>
                    <td>:</td>
                    <td><input name="key" size="30" type=text></td>
                </tr>
                <tr>
                    <td colspan="3" align="right">
                        <input type="submit" name="submit" value="save">
                    </td>
                </tr>
            </table>
            <?php echo form_close() ?>
        </div>
    </form>
</body>
</html>