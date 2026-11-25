CAMPUS SPORTS FACILITY BOOKING SYSTEM
SPG 0553 Advance Web Programming (LARAVEL) - Mini Project
=====================================================================

Students and the public book sports courts online. Students book for
free (max 2 upcoming bookings); external users pay a fee per slot,
recorded by the admin, who also manages facilities, opening hours,
time slots, bookings, payments and user accounts.

Stack: Laravel 13, Livewire 4 starter kit, Laravel Fortify (login,
registration, 2FA, passkeys), Flux UI (free), Alpine.js, Tailwind CSS 4,
Pest 4. Database: MySQL (XAMPP's MariaDB 10.4 works). Tests use an
in-memory SQLite database so they never touch your real data.


1. REQUIREMENTS
---------------------------------------------------------------------
- PHP 8.3 or newer (tested with PHP 8.5 from XAMPP), with the
  pdo_mysql and pdo_sqlite extensions enabled
- MySQL 8 or MariaDB 10.4+ (XAMPP's MySQL is fine)
- Composer 2
- Node.js 20+ and npm
- Git


2. ENVIRONMENT SETUP
---------------------------------------------------------------------
  composer install
  npm install
  copy .env.example .env          (Windows)   /  cp .env.example .env
  php artisan key:generate

Database - MySQL (default):
  1. In C:\xampp\php\php.ini make sure this line has no ";" in front:
       extension=pdo_mysql
  2. Start MySQL in the XAMPP Control Panel.
  3. Create a database named sports_booking
     (phpMyAdmin > New, or run:
      C:\xampp\mysql\bin\mysql -u root -e "CREATE DATABASE sports_booking")
  4. .env (copied from .env.example) already has:
       DB_CONNECTION=mysql
       DB_HOST=127.0.0.1
       DB_PORT=3306
       DB_DATABASE=sports_booking
       DB_USERNAME=root
       DB_PASSWORD=

Database - SQLite (quick alternative, no server needed):
  Set DB_CONNECTION=sqlite in .env, delete the other DB_ lines and
  create the empty file database\database.sqlite.


3. COMMANDS
---------------------------------------------------------------------
  php artisan migrate:fresh --seed   create tables and demo data
  npm run build                      build CSS/JS once
  php artisan serve                  run at http://localhost:8000

  composer run dev                   server + Vite + queue + logs together
                                     (use instead of the two above while coding)

  php artisan test                   run all Pest tests
  php artisan test --filter=Booking  run only matching tests

The demo slots cover today and the next 7 days from the day you seed.
Run "php artisan migrate:fresh --seed" again before a demo so the dates
are current.


4. DEFAULT LOGINS (password for every account: password)
---------------------------------------------------------------------
  Role       Email               Notes
  Admin      admin@gmi.test      Full access to /admin pages
  Student    student@gmi.test    Ahmad Faris - already at the 2-booking limit
  Student    aina@gmi.test       Nurul Aina
  Student    weijie@gmi.test     Lim Wei Jie - has a cancelled booking
  External   external@gmi.test   Siti Rahmah (alumni) - one unpaid, two paid
  External   rajesh@gmi.test     Rajesh Kumar - one unpaid, one paid

New users can register at /register. Ticking "I am a GMI student" asks
for a student ID and gives the student role; otherwise the account is
an external user. The role is always set on the server, so nobody can
register as an admin.


5. MODULE ALLOCATION
---------------------------------------------------------------------
(Edit to match what each member actually built and presents.)

Nor Afiqri bin Nor Azari - Security & user management
  - Role column, Role enum, registration roles (CreateNewUser)
  - "access-admin" Gate in AppServiceProvider (denyAsNotFound), used
    as can:access-admin middleware on every /admin route
  - All 7 Policies
  - Admin users pages: UpdateUser, DeleteUser actions, UserForm
  - Tests: AccessControlTest, PolicyTest, RegistrationRoleTest

Muhammad Aiman bin Harun - Bookings
  - bookings table, Booking model, BookingStatus enum
  - CreateBooking (transaction + row lock, 2-booking limit, payment
    deadline), CancelBooking, UpdateBookingStatus actions
  - Facility page booking modal, My bookings page, booking details,
    admin bookings list and details
  - Tests: CreateBookingTest, CancelBookingTest

Muhammad Aliff Imran bin Mohd Faizal - Facilities, schedules & slots
  - facility_types, facilities, facility_schedules, time_slots tables
    and models
  - FacilityTypeController (Resource Controller, all 7 RESTful
    actions), StoreFacilityTypeRequest / UpdateFacilityTypeRequest,
    Blade views with @extends, @csrf, @method, old(), <x-forms.error>
  - SaveFacilityType, DeleteFacilityType, SaveFacility,
    DeactivateFacility, SaveFacilitySchedule, GenerateTimeSlots,
    SetSlotBlocked actions; FacilityTypeForm, FacilityForm,
    ScheduleForm, GenerateSlotsForm
  - Public facility list, admin facility types / facilities /
    opening hours / time slots pages
  - Tests: FacilityTypeControllerTest, AdminManagementTest

Syazwan Hadif bin Shaiful Azmi - Payments, dashboard & Alpine UI
  - payments table, Payment model, PaymentMethod/PaymentStatus enums
  - RecordPayment action (receipt numbers), PaymentForm
  - Admin payments page, record-payment modal, printable receipt
  - Dashboard for each role, layouts and navigation, home page
  - Alpine.js: booking tabs, cancel modal, bank-reference toggle,
    student ID toggle on registration, print button
  - Seeders and factories; Tests: RecordPaymentTest, DomainRulesTest


6. WHERE EACH REQUIREMENT LIVES
---------------------------------------------------------------------
  FR      Page (route name)                     Action / Policy
  S1,E1   register                              Fortify CreateNewUser
  S2,E2   facilities.index, facilities.show     FacilityPolicy@view
  S3,E3   facilities.show (confirm modal)       CreateBooking, BookingPolicy@create
  S4      (inside CreateBooking)                STUDENT_ACTIVE_LIMIT = 2
  S5,E4   bookings.index, bookings.show         CancelBooking, BookingPolicy@cancel
  S6,E5   bookings.index, bookings.show,        BookingPolicy@view,
          payments.receipt                      PaymentPolicy@viewReceipt
  A1      admin.facility-types.* (Route::resource)  FacilityTypeController,
                                                Form Requests -> SaveFacilityType,
                                                DeleteFacilityType
  A2      admin.facilities.index                SaveFacility, DeactivateFacility
  A3      admin.facilities.schedules.index      SaveFacilitySchedule
  A4      admin.slots.index                     GenerateTimeSlots, SetSlotBlocked
  A5      admin.bookings.index / .show          UpdateBookingStatus
  A6,A7   admin.payments.index, admin.bookings.show   RecordPayment
  A8      admin.users.index / .show             UpdateUser, DeleteUser

Folder guide:
  app/Actions/          business rules, one class per task
  app/Http/Controllers/Admin/  FacilityTypeController (resource controller)
  app/Http/Requests/Admin/     Form Requests used by that controller
  app/Enums/            Role, BookingStatus, SlotStatus, PaymentMethod, PaymentStatus
  app/Livewire/Forms/   Livewire Form Objects (validation rules)
  app/Policies/         one Policy per core entity
  resources/views/pages/  Livewire single-file page components (the ⚡ files)
  database/seeders/     demo data for every role
  tests/Feature, tests/Unit   Pest tests


7. CHANGES FROM THE DESIGN DOCUMENT
---------------------------------------------------------------------
- Routes: Facility types use a full Resource Controller, exactly as in
  section 7.3 of the design (Route::resource, all 7 actions). The other
  admin resources are Livewire pages that do create, edit and delete in
  Flux modals, so their store/update/destroy routes are Livewire actions
  guarded by the same Policies. Every page in sections 7.1-7.8 exists
  with the same route name, except:
    * facilities.slots.index is part of facilities.show (?date=...)
    * bookings.create / bookings.store are the confirm modal on
      facilities.show
    * admin.slots.generate is a modal on admin.slots.index
- Admin routes are guarded by the access-admin Gate. Non-admins get a
  404 (not 403), so the admin area is not revealed.
- users.name and users.email keep Laravel's default length (255).
- Pending external bookings past their deadline are shown in red on
  the payments page; the admin cancels them by hand. (Automatic expiry
  would be a scheduled job - a possible extension.)


SYLLABUS COVERAGE (where to show each topic in the demo)
---------------------------------------------------------------------
  T1 MVC / 3-tier     Models (app/Models), Views (resources/views),
                      Controllers + Livewire pages, Action classes
  T2 Environment      PHP 8.5, Composer, Node/Vite, MySQL, Git, Pest
  T3 Structure/config .env (DB_*, APP_NAME), artisan commands above
  T4 Routing & Blade  routes/web.php: named routes, route model
                      binding, Route::resource, Route::fallback (404);
                      layouts/master.blade.php with @yield, child views
                      with @extends / @section / @include;
                      $loop->even and $loop->first in
                      admin/facility-types views; {{ }} escaping
  T5 Data tier        7 migrations with up()/down() and FK onDelete
                      rules; $fillable; belongsTo / hasMany / hasOne /
                      hasManyThrough; when() filters; enums; factories
                      with User::factory() references; seeders
  T6 Controllers/CRUD FacilityTypeController: index, create, store,
                      show, edit, update, destroy; @csrf;
                      @method('PUT') and @method('DELETE'); 404 via
                      route model binding; validation with old() and
                      @error; reusable <x-forms.error>; flash messages
  T7 Security         auth / verified middleware groups;
                      Gate access-admin + can: middleware +
                      denyAsNotFound(); a Policy per model with
                      Gate::authorize() / $this->authorize(); @can in
                      views; ownership scoping
                      (auth()->user()->bookings()); Fortify starter kit
                      with login throttling; role set server-side
  T8 Patterns & QA    Form Requests + Action classes + skinny
                      controller; the same Actions reused by Livewire;
                      Livewire (wire:model, wire:submit, wire:loading,
                      wire:confirm, debounce) + Alpine (x-data, x-show,
                      x-on:click, $wire, tabs, modals); Pest unit,
                      feature and Livewire tests, datasets, actingAs,
                      RefreshDatabase, data-test attributes
