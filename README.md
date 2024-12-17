# m7011e

Setup
1. Install XAMPP
    Download XAMPP.
    Install and open XAMPP Control Panel.

2. Start Apache and MySQL
    In XAMPP, click Start next to Apache and MySQL to run the local server and database.

3. Download the Project
    Clone or download the repository.
    git clone https://github.com/AronGunnar/m7011e
    Place the project folder in C:/xampp/htdocs/.

4. Configure Database
    Open db_connection.php.
    Update MySQL credentials:
    $servername = "fuxcp.h.filess.io";
    $username = "m7011e_recalltea";
    $password = "db08c1af076bb0f865adc178705b4ed4c8318e4c";
    $dbname = "m7011e_recalltea";

5. Access the Project
    Open your browser and go to:
    http://localhost:8080/your-project-folder/index.html (or whatever your Apache port is)
