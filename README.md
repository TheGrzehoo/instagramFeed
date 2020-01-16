# Aplikacja do integracji z instagramem.
Aplikacja pobiera dane klienta, a następnie generuje odpowiedni link, przetwarza otrzymany kod i na jego podstawie generuje tokeny.
Pobrane dane zawartości z instagrama zapisywane są do pliku JSON, który jest dostępny pod odpowiednią bramką.

Aplikacja automatycznie odświeża long lived token potrzebny do pozyskiwania zdjęć z instagrama. 

Odświeżanie zawartości bramki odbywa się raz dziennie lub na polecenie użytkownika.

## Done:
- rejestrowanie klucza i dostępu
- warstwa wizualna formularza do uzupełnienia
- podpięcie bazy danych
- pobieranie zawartości instagrama

## TODO
- podpięcie warstwy frontendowej (React),
- pozyskiwanie danych klienta do odpalenia aplikacji
