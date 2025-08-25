# Installation & Update

## Fresh Installation
1. Upload the project files and ensure the `public/` directory is web accessible.
2. Browse to `/install.php` and verify that your server meets the PHP version and extension requirements.
3. Provide MySQL credentials in the form. The installer will create the database schema from the `sql/` folder and write a `.env` file.
4. `composer install` is executed automatically if vendors are missing.
5. Upon success an `installed.lock` file is created. **Delete `install.php`** after running the installer.

## Updating
1. Place `/update.php` on the server and open it in your browser. The script reads the current version from `version.txt`.
2. When confirmed it downloads the latest release from the repository, backs up existing files, extracts the update, and restores your `.env`.
3. Composer dependencies and database migrations are executed automatically.
4. `version.txt` is updated and you are prompted that the update is complete. **Delete `update.php`** when finished.
5. The updater skips `install.php` and `update.php`, so once removed these scripts stay absent in future releases.

## Security Notes
- Restrict access to `install.php` and `update.php` or remove them entirely after use.
- Backup your database and files before performing updates.
- Never leave the installer or updater scripts on a production system.
