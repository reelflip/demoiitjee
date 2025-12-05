IITGEEPrep Backend Deployment Instructions
==========================================

1. DATABASE:
   - Create a MySQL Database in Hostinger.
   - Import 'database.sql'.

2. API FILES:
   - The 'api' folder contains all PHP scripts.
   - Edit 'api/config.php' if your DB credentials change.
   - Permissions: Folders (755), Files (644).

3. FRONTEND:
   - Upload your React build (index.html, assets/) to public_html.
   - Upload .htaccess to public_html to fix routing.

4. TROUBLESHOOTING:
   - Visit https://iitgeeprep.com/api/test_db.php to check connection.