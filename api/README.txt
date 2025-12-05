IITGEEPrep (v2.5 Testing & Analytics Release) - API DEPLOYMENT
============================================
Website: iitgeeprep.com

QUICK SETUP GUIDE:
1. UPLOAD: Extract and upload all .php files to public_html/api folder.
   NOTE: Ensure they are in public_html/api, not public_html/api/api.
   URL Check: iitgeeprep.com/api/index.php should return JSON.

2. CONFIG: Open config.php and verify your Hostinger database credentials.
   - Host: 82.25.121.80
   - User: u131922718_iitjee_user
   - DB: u131922718_iitjee_tracker
   - Pass: (Ensure you set your password!)

3. PERMISSIONS: 
   - Right click 'api' folder -> Permissions -> 755
   - Right click all .php files -> Permissions -> 644

4. DATABASE: Import the latest database.sql file via phpMyAdmin.

5. TEST: Visit iitgeeprep.com/api/test_db.php to verify connection.

TROUBLESHOOTING:
- JSON Errors: This zip has 'error_reporting(0)' enabled to prevent PHP warnings.
- 403 Forbidden: Check permissions.
- 500 Error: Check config.php for typo in password.
