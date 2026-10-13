# MathLab – dynamic course support website

A small website built with **PHP 8 + MySQL/MariaDB**. It has user login (roles:
**Tutor** and **Student**), announcements, course documents, assignments and a
contact form. The website text is in Greek.

It runs on your own computer with **XAMPP** – no university server (such as
`webpages.auth.gr`) is needed.

## Requirements
- [XAMPP](https://www.apachefriends.org/) (includes Apache, PHP and MySQL/MariaDB), PHP 8.0 or newer.

## Installation (Windows / XAMPP)
1. Download this project from GitHub: **Code → Download ZIP**, then unzip it.
2. Copy the `mathlab` folder into `C:\xampp\htdocs\`
   (you should end up with `C:\xampp\htdocs\mathlab`).
3. Open the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.
4. Open <http://localhost/phpmyadmin> in your browser.
5. Click **Import** → **Choose file** → select `mathlab/sql/mathlab.sql` → click **Import** at the bottom.
   This creates the database `mathlab` with all tables and demo data.
6. Open <http://localhost/mathlab/login.php>

On macOS / Linux the folder is `/Applications/XAMPP/htdocs` or `/opt/lampp/htdocs`.

## Demo accounts
| Role | Login | Password |
|---|---|---|
| Tutor | tsitlakidis@mathlab.tutor.gr | tutor123 |
| Student 1 | student1@mathlab.student.gr | student123 |
| Student 2 | student2@mathlab.student.gr | student456 |

These are demo accounts for local testing only. **Change or delete them before
putting the site on a public server.**

## Database settings
By default the project connects as MySQL user `root` with **no password**
(the XAMPP default) to the database `mathlab`. If your MySQL uses other
details, copy `includes/config.local.example.php` to `includes/config.local.php`
and edit the values there. `config.local.php` is ignored by git, so your
password never gets uploaded to GitHub.

## Project structure
| Path | Purpose |
|---|---|
| `*.php` (root) | The pages of the site (login, announcements, documents, ...) |
| `includes/` | Shared code: settings, database, login/roles, header, menu, upload helper |
| `sql/mathlab.sql` | Creates the database, tables and demo data |
| `css/`, `images/` | Styling and pictures |
| `files/`, `assignments/` | Uploaded course documents and assignment files |

## Known limitations
- The contact form uses PHP's `mail()`. On a local XAMPP it **does not send real
  e-mail** unless you configure SMTP; the rest of the site works normally.
- `files/` and `assignments/` must be writable by the web server for uploads.
- Only these upload types are accepted: pdf, doc, docx, xls, xlsx, ppt, pptx,
  odt, ods, odp, txt, rtf, zip, png, jpg, jpeg (see `includes/upload.php`).
