IITGEEPrep (v1.8 Final Production) - API DEPLOYMENT
============================================
Website: iitgeeprep.com

QUICK SETUP GUIDE:
1. UPLOAD: Extract and upload all .php files to public_html/api folder.
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
6. SEO: Upload sitemap.xml and robots.txt to public_html root.

TROUBLESHOOTING:
- 403 Forbidden: Check permissions or delete .htaccess in api folder temporarily.
- 500 Error: Check config.php for typo in password.
