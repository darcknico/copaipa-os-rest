ionic cordova build android --prod --release

jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore copaipaos.keystore platforms\android\app\build\outputs\apk\release\app-release-unsigned.apk copaipaos -storepass copaipaos

C:\Users\nicol\AppData\Local\Android\Sdk\build-tools\28.0.3\zipalign -v 4 platforms\android\app\build\outputs\apk\release\app-release-unsigned.apk copaipaos.apk


cd D:\Android\build-tools\28.0.1

desarrollo version
id="ar.com.proyectosinformaticos.copaipa_os" version="0.0.5"

producion version
id="ar.org.copaipa.copaipa_os" version="0.0.2"

keytool -genkey -v -keystore copaipaos.keystore -alias copaipaos -keyalg RSA -keysize 2048 -validity 10000
