# m7011e

Setup
1. Install XAMPP<br>
    Download XAMPP.<br>
    Install and open XAMPP Control Panel.<br>

2. Start Apache and MySQL<br>
    In XAMPP, click Start next to Apache and MySQL to run the local server and database.<br>

3. Download the Project<br>
    Clone or download the repository.<br>
    git clone https://github.com/AronGunnar/m7011e<br>
    Place the project folder in "C:/xampp/htdocs/".<br>

4. Configure Database<br>
    Open db_connection.php.<br>
    Update MySQL credentials:<br>
    $servername = "fuxcp.h.filess.io";<br>
    $username = "m7011e_recalltea";<br>
    $password = "db08c1af076bb0f865adc178705b4ed4c8318e4c";<br>
    $dbname = "m7011e_recalltea";<br>

5. Access the Project<br>
    Open your browser and go to:<br>
    http://localhost:8080/your-project-folder/index.php (or whatever your Apache port is)<br>
