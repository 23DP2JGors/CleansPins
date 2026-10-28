<p align="center">
  <b>🚗 CleansPins</b><br>
  Interneta veikals auto kopšanas precēm
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="#"><img src="https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs&logoColor=white" alt="Vue"></a>
  <a href="#"><img src="https://img.shields.io/badge/Inertia.js-2.x-9553E9" alt="Inertia.js"></a>
  <a href="#"><img src="https://img.shields.io/badge/Vuetify-3-1867C0?logo=vuetify&logoColor=white" alt="Vuetify"></a>
  <a href="#"><img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite"></a>
  <a href="#"><img src="https://img.shields.io/badge/Pest-3.8-FF6E9C" alt="Pest"></a>
</p>

## Par projektu

**CleansPins** (AutoCare) ir pilnas steka (full-stack) tīmekļa lietojumprogramma — interneta
veikals auto kopšanas precēm. Sistēmas galvenā priekšrocība salīdzinājumā ar vispārīgiem
preču katalogiem ir iespēja atlasīt preces pēc **automašīnas zonas** (virsbūve, salons,
diski, stikli, riepas) un pēc **konkrētas problēmas** (traipi, skrāpējumi u.c.), kā arī
pilnībā latviski noformēta pasūtīšanas plūsma ar piegādi uz pakomātu (Omniva, DPD,
Latvijas Pasts).

Projekts izstrādāts kā kvalifikācijas darbs. Visi datu bāzes, modeļu un tabulu nosaukumi
projektā ir latviešu valodā.

## Funkcionalitāte

**Pircējam (reģistrētam vai nereģistrētam lietotājam):**
- preču kataloga pārlūkošana un filtrēšana pēc kategorijas, zonas un problēmas;
- reģistrēšanās un pieteikšanās sistēmai;
- groza pārvaldība (preces pievienošana, daudzuma maiņa, noņemšana);
- pasūtījuma noformēšana ar piegādi uz izvēlēto pakomātu;
- pasūtījumu vēstures skatīšana un aktīva pasūtījuma atcelšana;
- profila datu un paroles maiņa, konta dzēšana.

**Administratoram:**
- preču, kategoriju, zonu un problēmu pārvaldība (CRUD);
- visu sistēmas pasūtījumu skatīšana un statusa maiņa;
- lietotāju kontu pārvaldība.

## Tehnoloģiju steks

| Slānis | Tehnoloģija |
|---|---|
| Serveris (backend) | PHP 8.2+, Laravel 12 |
| Savienotājslānis | Inertia.js 2 |
| Klients (frontend) | Vue 3, Vuetify 3 |
| Resursu kompilēšana | Vite |
| Datubāze | SQLite (izstrādei/testiem) / MySQL (produkcijai) |
| Testēšana | Pest 3.8 |

## Prasības

- PHP ≥ 8.2 ar paplašinājumiem, ko pieprasa Laravel 12
- Composer 2.x
- Node.js ≥ 18 un npm
- SQLite (vai MySQL, ja izvēlēta tā)

## Instalācija

```bash
git clone https://github.com/23DP2JGors/cleanspins.git
cd cleanspins

# PHP un JS atkarības
composer install
npm install

# Vides fails un lietojumprogrammas atslēga
cp .env.example .env
php artisan key:generate

# Datubāze (SQLite pēc noklusējuma, sk. .env DB_* mainīgos)
touch database/database.sqlite
php artisan migrate --seed
```

> Visus iepriekšējos soļus (izņemot `git clone`) var veikt arī ar vienu komandu:
> `composer run setup`.

## Lietojumprogrammas palaišana izstrādes vidē

```bash
composer run dev
```

Šī komanda vienlaicīgi palaiž Laravel serveri, rindu (queue) klausītāju, logu skatītāju
(`pail`) un Vite ar karsto pārlādi (HMR). Lietojumprogramma būs pieejama adresē
`http://127.0.0.1:8000`.

Ja nepieciešams palaist daļas atsevišķi:

```bash
php artisan serve   # Laravel serveris
npm run dev          # Vite (frontend)
```

## Testēšana

Projektā izmantots [Pest](https://pestphp.com) testēšanas ietvars.

```bash
composer test
# vai
php artisan test
```

Testi atrodas direktorijā `tests/Feature` (funkcionālie testi pa moduļiem — katalogs,
grozs, pasūtījumi, profils, administratora panelis u.c.) un `tests/Unit`.

## Projekta struktūra

Datubāzes tabulas, Eloquent modeļi un to lauki ir nosaukti latviešu valodā:

| Tabula | Apraksts |
|---|---|
| `lietotaji` | Sistēmas lietotāji (pircēji un administratori) |
| `kategorijas` | Preču kategorijas |
| `zonas` | Automašīnas zonas (virsbūve, salons, diski, stikli, riepas) |
| `problemas` | Risināmās problēmas (traipi, skrāpējumi u.c.) |
| `produkti` | Preces |
| `produktu_zonas` / `produktu_problemas` | Preces piesaiste zonām/problēmām (M:N) |
| `groza_rindas` | Lietotāju grozu saturs |
| `pasutijumi` / `pasutijuma_rindas` | Pasūtījumi un to rindas |
| `pakomati` | Piegādes pakomāti (Omniva, DPD, Latvijas Pasts) |

Galvenās koda direktorijas:

```
app/Http/Controllers/     — kontrolieri (t.sk. Admin/ apakšdirektorija administratora panelim)
app/Models/                — Eloquent modeļi
database/migrations/       — datubāzes migrācijas
database/seeders/          — testa datu ģeneratori
resources/js/Pages/        — Inertia/Vue lapas
resources/js/Components/   — Vue komponentes
routes/web.php             — maršruti
tests/Feature/             — funkcionālie Pest testi
```

## Komandas dalībnieki

| Vārds, uzvārds | Loma |
|---|---|
| Daniels Veliks | Frontend izstrāde |
| Egors Goršakovs | Frontend izstrāde |
| Dmitrijs Tumanovs | Backend izstrāde |
| Glebs Zlotņikovs | Testēšana |

## Licence

Projekts balstīts uz [Laravel](https://laravel.com) ietvaru, kas izplatīts ar
[MIT licenci](https://opensource.org/licenses/MIT).
