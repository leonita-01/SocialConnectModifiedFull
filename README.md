SocialConnect Project
Përmbledhje
Ky projekt është një sistem social që përfshin një aplikacion të ndarë në dy pjesë kryesore: pjesën e klientit dhe pjesën e serverit. Projekti është zhvilluar duke përdorur Vue.js për pjesën e klientit dhe Laravel për pjesën e serverit. Pjesa e serverit përmban një API të plotë RESTful dhe përdor Laravel Passport për autentikim dhe autorizim.

Kërkesat Funksionale
Sistemi duhet të zhvillohet në atë mënyrë që pjesa e klientit dhe e serverit duhet të jenë të pavarura në mes veti.
Komunikimi në mes të klientit dhe serverit duhet të bëhet duke përdorur protokolin e komunikimit: HTTP/HTTPS.
Pjesa e serverit duhet të përmbajë së paku 20 pika fundore.
Pjesa e serverit duhet të zhvillohet duke përdorur korniza të punës (frameworks) të cilat e mundësojnë krijimin e RESTFull API.
Kodi duhet të jetë i shkruar në paradigmen programuese: POO.
API i krijuar në pjesën e Serverit duhet të dokumentohet duke e përdorur Swagger UI - https://swagger.io/tools/swagger-ui/
Përdorimi i ORM-së për komunikim me bazë të të dhënave.
Krijimi i sistemit për Autentikim dhe Autorizim.
Përdorimi i middleware.
Përdorimi i ContextProvider në pjesën e klientit, ose diçka të ngjashme.
Përdorimi i unit-testeve dhe API-testeve - duke mbuluar pjesët e kodit në aplikacion.
Krijimi i së paku 20 modeleve - migrimet.
Përdorimi i Formik ose diçka të ngjashme në menaxhimin e formave të klientit.
Hapat për Vendosje dhe Testim
1. Instalimi i Varësive
Përdorni Composer për të instaluar varësitë për Laravel:

bash
Copy code
composer install
Përdorni npm për të instaluar varësitë për Vue.js:

bash
Copy code
cd client
npm install
2. Konfigurimi i Mjedisit
Kopjoni skedarin .env.example dhe riemërtojeni në .env:

bash
Copy code
cp .env.example .env
Gjeneroni çelësin e aplikacionit:

bash
Copy code
php artisan key:generate
3. Migrimi i Bazës së të Dhënave
Migroni bazën e të dhënave dhe futni të dhënat fillestare:

bash
Copy code
php artisan migrate
4. Instalimi i Passport
Instaloni Passport dhe gjeneroni çelësat e enkriptimit:

bash
Copy code
php artisan passport:install --force
5. Shërbimi i Aplikacionit
Startoni serverin Laravel:

bash
Copy code
php artisan serve
Startoni aplikacionin klient:

bash
Copy code
cd client
npm run serve
6. Testimi i Pikave Fundore
Përdorni Postman për të testuar pika të ndryshme fundore (p.sh., regjistrim, login, krijim i postimeve, etj.).

7. Dokumentimi i API-së
Vizitoni endpoint-in për dokumentimin e API-së në Swagger UI (zakonisht http://127.0.0.1:8000/api/documentation).

8. Testimi i Autentikimit dhe Autorizimit
Sigurohuni që regjistrimi, login-i dhe përdorimi i token-eve të punojnë si duhet.

9. Kontrollimi i Rrugëve të API-së
Për të parë të gjitha rrugët e regjistruara dhe për të siguruar që janë të sakta:

bash
Copy code
php artisan route:list
Përmbledhje
Ky projekt përfshin një sistem të plotë autentikimi dhe autorizimi, menaxhimin e përdoruesve, miqësitë, postimet, komentet, grupet, historitë dhe më shumë. Përdorimi i Laravel Passport siguron një autentikim të sigurt dhe menaxhimin e autorizimeve, ndërsa Vue.js ofron një ndërfaqe përdoruesi të shpejtë dhe të përgjegjshme.
