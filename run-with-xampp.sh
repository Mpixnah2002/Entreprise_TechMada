#!/bin/bash
# Lance le serveur de développement CodeIgniter avec le PHP de XAMPP
cd "$(dirname "$0")/ci4" || exit 1
/opt/lampp/bin/php spark serve
