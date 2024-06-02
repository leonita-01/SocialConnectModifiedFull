1. Sistemi duhet të zhvillohet në atë mënyrë që pjesa e klientit dhe e serverit të jenë të pavarura në mes veti.
Pjesa e klientit dhe serverit janë ndarë në direktorë të veçantë (client dhe server).
2. Komunikimi në mes të klientit dhe serverit duhet të bëhet duke e përdorur protokolin e komunikimit: HTTP/HTTPS.
Serveri Laravel është konfiguruar për të përdorur HTTPS.
3. Pjesa e serverit duhet të përmbajë së paku 20 pika fundore.
Kemi krijuar disa pika fundore në routes/api.php.
4. Pjesa e serverit duhet të zhvillohet duke përdorur korniza të punës (frameworks) të cilat e mundësojnë krijimin e RESTFull API.
Kemi përdorur Laravel për serverin dhe kemi krijuar RESTful API.
5. Kodi duhet të jetë i shkruar në paradigmen programuese: POO.
Kemi përdorur Laravel, i cili ndjek paradigmën e programimit të orientuar objekt.
6. API i krijuar në pjesën e Serverit duhet të dokumentohet duke e përdorur Swagger UI.
Kemi instaluar dhe konfiguruar L5-Swagger për dokumentimin e API-së.
7. Përdorimi i ORM-së për komunikim me bazë të të dhënave.
Kemi përdorur Eloquent ORM që vjen me Laravel për komunikim me bazën e të dhënave.
8. Krijimi i sistemit për Autentikim dhe Autorizim.
Kemi instaluar Laravel Passport për autentikim dhe autorizim.
9. Përdorimi i middleware.
Kemi përdorur middleware për autentikim dhe autorizim.
10. Përdorimi i ContextProvider në pjesën e klientit, ose diçka të ngjashme.
Përdorim Vuex për menaxhimin e gjendjes në aplikacionin klient (Vue.js).
11. Përdorimi i unit-testeve dhe API-testeve - duke mbuluar pjesët e kodit në aplikacion.
Kemi krijuar disa teste bazike në Laravel dhe kemi planifikuar të shtojmë më shumë teste për të mbuluar pjesët e kodit.
12. Krijimi i së paku 20 modeleve - migrimet.
Kemi krijuar modelet dhe migrimet e nevojshme për të përmbushur kërkesat.
13. Përdorimi i Formik ose diçka të ngjashme në menaxhimin e formave të klientit.
Në Vue.js, përdorim vee-validate për validimin dhe menaxhimin e formave.
