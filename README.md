Ideal CMS v. 5.0
=========

Система управления контентом с открытым исходным кодом, написанная на PHP.

Используемые технологии и продукты:

* PHP 7.4+,
* MySQL 5+,
* MVC, 
* PSR-0, PSR-1, PSR-2
* Twig, 
* jQuery,
* Twitter Bootstrap 3,
* CKEditor,
* CKFinder, 
* FirePHP.

Все подробности на сайте [idealcms.ru](http://idealcms.ru/)

Версия 5.0
---
1. FIX: переход на распространение через Composer

Версия 4.7.1
---
1. FIX: получение prev_structure из активной модели
2. ADD: возможность указывать период проверки для файлового мониторинга
3. FIX: не проверять ssl-сертификат в curl
4. FIX: ошибка в подготовке данных для XML Турбо-страниц Яндекса
5. FIX: подключение файлов
6. FIX: правильное формирование запроса в классе поля Url

Версия 4.7
---
1. DEL: определение и установка flash-cookies
2. ADD: указание необходимости добавление параметра -f для sendmail в скрипте файлового мониторинга
3. FIX: ошибка отображения данных из фото-аддона, если ни одной фотографии нет
4. FIX: работа скрипта в связке с php cron (в скрипте не должно быть exit и die)
5. ADD: в функции dateReach добавлена возможность указывать или не указывать суффикс для даты с месяцем в текстовом виде
6. ADD: возможность изменять размеры картинок со сторонних ресурсов, а не только локальных
7. ADD: недостающие картинки для jQuery
8. ADD: возможность отключать использование smtp через настройки админки


Версия 4.6
---
1. FIX: почта отправляется не в quoted-printable, а в base64
2. ADD: возможность не логировать ошибки в классе Db
3. FIX: из консоли уведомление об ошибке приходит с названием выполненного скрипта, а не страницы
4. ADD: отправка всей почты через SMTP подключение и возможность деактивировать отправку через SMTP
5. ADD: возможность отключить указание дополнительного параметра From при отправке писем через Sendmail
6. FIX: определение ссылок, разбитых на несколько строк при парсинге карты сайта
7. FIX: при неправильном ajax-запросе выдаётся 404-ошибка в дизайне сайта
8. UPD: версия FirePHP
9. ADD: вывод trace для кастомных некритичных ошибок AddError

Версия 4.5.5
---
1. ADD: сплющивание дерева элементов в одномерный массив в модели Cid
2. ADD: возможность указать Bcc в классе отправки сообщений
3. FIX: в canonical не будет учитываться get-параметр page 
4. FIX: вычисление url для элемента по id
5. FIX: уменьшение картинок с русскими буквами в именах файлов
6. FIX: вставка булевых значений
7. FIX: удаление элементов БД с указанием только sql для where
8. FIX: формирование запроса для поиска по сайту через Yandex XML
9. FIX: вырезание заголовка из контента
10. FIX: отправка формы через iframe теперь идёт только если в опциях отправки указан iframeSend
11. FIX: получение элементов вложенного раздела для html-карты сайта
12. FIX: построение двухуровневого меню, если не указана prevStructure

Версия 4.5.4
---
1. FIX: формат rss-фида, формируемого для турбо-страниц Яндекса
2. FIX: упрощённый метод указания дополнительных http-заголовков для ajax-контроллера
3. FIX: в аддоне поиска передача запроса в шаблон даже в случае если ничего не найдено
4. FIX: при генерации турбо-страниц чистим код от неподходящих для XML символов
5. FIX: вызов экшенов контроллера из других контроллеров
6. FIX: доработка фреймворка форм
7. FIX: механизм извлечения ссылок со страницы при составлении карты сайта
8. FIX: обработка вложенных url в новостях
9. FIX: более правильное указание адреса отправителя при отправке почты
10. ADD: возможность сделать редирект после заполнения формы
11. ADD: построение url по cid и по prev_structure
12. ADD: вложенность в тегах

Версия 4.5.3
---
1. FIX: виджет двухуровневого меню
2. FIX: ссылки на актуальный jquery в DateTime Picker
3. ADD: метод __isset в классе Config
4. FIX: вывод текста о сохранении настроек

Версия 4.5.2
---
1. ADD: возможность не извлекать заголовок из текста страницы
2. FIX: отправка письма по SMTP-протоколу
3. ADD: поле для обработки капчи от Google для FormPhp
4. FIX: отсчёт времени выполнения скрипта сбора турбо-страниц начинается сразу же, а не после разбора файлов
5. FIX: уведомление об ошибке при выполнении запроса к БД
6. FIX: устранение ошибки при старте сбора карты сайта

Версия 4.5.1
---
1. FIX: более правильная конструкция файлового кэширования для .htaccess
2. ADD: в .htaccess (из коробки) присутствует конструкция для обеспечения сжатия контента
3. FIX: теги \<li\>, \<h3\>, \<h4\>, \<h5\> добавлены к списку разрешённых для присутствия в контенте турбо-страницы
4. FIX: построение хлебных крошек для главной страницы
5. FIX: в списке событий лога показывать и собственно текст самого события
6. FIX: возможность получения description не в xhtml формате при составлении турбо-фида

Версия 4.5
---
1. FIX: обновление CMS по https
2. FIX: построение пути для элементов html-карты сайта
3. FIX: определение наличия подключения структуры 'Log' в файле обновления
4. FIX: сохранение в БД элементов структуры, в которой есть динамические поля, не хранящиеся в БД
5. FIX: вынос в админской части всех модальных окон и скриптов за пределы блока контента
6. FIX: Вывод кастомизированных сообщений после сохранения элемента в админке
7. ADD: получение parentModel в аддоне
8. DEL: связь заказов с заказчиками и перенос кода crm в модули
9. FIX: модель может задать свой контроллер для обработки
10. ADD: поля phone и client_id в заказе с сайта
11. FIX: в диагностические сообщения крона добавлен вывод даты следующего запуска запланированной задачи
12. FIX: обработка значений NULL при обновлении строки в таблице
13. FIX: фильтр заказов не по лидам, а по типу заказа

Версия 4.4.1
---
1. FIX: кнопка запуска обновления CMS прикрепляется к нужной форме, а не к последней
2. FIX: описание установки крона
3. FIX: в кроне выполняется только по одной задаче за один запуск
4. FIX: редирект со страницы с page=1 на страницу без параметра page

Версия 4.4
---
1. ADD: создание фида для турбо-страниц Яндекса  
2. ADD: управление через админку наличием строк в .htaccess отвечающих за браузерное кэширование
3. ADD: логирование действий пользователей в админке
4. FIX: обработка значений NULL при вставке строки в таблицу
5. FIX: более подходящая функция для подсчёта длины строки при обрезке суффикса ссылки

Версия 4.3.4
---
1. FIX: использование правильной модели аддона в зависимости от режима работы ('admin', 'site')
2. FIX: параметры конфигурации для работы аддона 'Я.Вебмастер' присутствуют сразу после установки
3. FIX: кастомизация переменной seance для модели User

Версия 4.3.3
---
1. ADD: сбор перелинковки только если установлена галка 'Собирать перелинковку'
2. ADD: возможность испльзовать класс Util без конфига (вне Ideal CMS)
3. ADD: формирование prev_structure для текущего элемента (для получения его потомков)

Версия 4.3.2
---
1. FIX: правильный протокол сайта в уведомлениях
2. FIX: для поля Select с заданными вариантами значений в списке элементов в админке выдаём эти значения, а не айдишники
3. FIX: обработка полей типа «SET» при изменении типа в базе данных на вкладке «Проверка целостности» в разделе «Сервис»
4. FIX: сохранение данных о дате последнего входа и счётчик неудачных попыток авторизации 
5. FIX: при установке отображается уведомление о неправильно введённом домене

Версия 4.3.1
---
1. FIX: возможность менять название параметра листалки новостей
1. FIX: параметр для предотвращения серверного кэширования при сборе карты сайта через админку

Версия 4.3
---
1. ADD: запуск задач крона и настройка их в админке
1. ADD: мониторинг изменений файлов
1. FIX: при прямом указании контроллера в url можно заменить слэши \ на точки (нужно для совместимости с RFC)
1. FIX: поле для загрузки файлов может быть подключено не только после всех остальных полей и валидаторов
1. FIX: документация FormPhp
1. FIX: год копирайта и добавление копирайтов в те файлы, где их не было
1. FIX: в карте сайта xlsx-файлы не анализируются на предмет ссылок в них

Версия 4.2.6
---
1. UPD: библиотеки DateTimePicker и Moment (security fix)
1. UPD: CKFinder (security fix)
1. FIX: поля работы с датой под обновлённые плагины работы с датой
1. FIX: работа с полем Ideal_Image
1. FIX: даже если в настройках нет пункта memcache система будет работать без сбоев
1. FIX: система инстанцирования модели Ideal_User, для возможности наследования

Версия 4.2.5
---
1. FIX: на странице авторизации отдаётся 404 заголовок
1. FIX: если запрашивается не json и не html версия страницы входа в админку, то считаем что к ней обращается бот и сообщаем об этом
1. FIX: https в поле Field\Url


Версия 4.2.4
---
1. FIX: вывод 404 страницы даже если она уже находится среди известных 404
1. FIX: получение списка всех новостей для html-карты сайта
1. FIX: в скрипте карты сайта не ищем ссылки в содержимом файлов xls, pdf, doc, docx

Версия 4.2.3
---
1. FIX: возможность формировать where-запросы к БД без указания обязательного массива с параметрами
1. FIX: работа с полем даты при пустых значениях даты
1. FIX: определение прав на корневые элементы CMS 

Версия 4.2.2
---
1. FIX: проверка наличия кода Google Tag Manager

Версия 4.2.1
---
1. ADD: событие gtm на отправку формы
1. FIX: скрипт карты сайта корректно обрабатывает protocol-agnostic ссылки
1. FIX: поддержка PHP 5.3

Версия 4.2
---
1. ADD: файл обновления для перемещения папки '_thumbs' из корня в папку 'tmp' со всем содержимым
1. FIX: notice от YandexWebmasterAddon при создании страницы
1. FIX: notice когда в поле сортировки не указан порядок сортировки

Версия 4.1.1
---
1. FIX: при роутинге, после входа во вложенную структуру и остуствия там продолжения роутинга, не сбрасывался путь в эту структуру
1. FIX: модели для аддона 'YandexWebmaster'

Версия 4.1
---
1. ADD: Micro CRM - распределение заказов по заказчикам, возможность объединять заказчиков с разными контактами
1. ADD: сортировка элементов в админке по отображаемы полям
1. ADD: отправка сообщений посредством SMTP через FormPhp
1. ADD: ограничение на размер анализируемой страницы в карте сайта - 3МБ страницы и файлы большего размера не парсятся
1. ADD: отправка текстов в Яндекс.Вебмастер
1. ADD: новый вид роутинга - API
1. FIX: поля ввода в аддоне фотогалереи
1. FIX: поиск соответствия запрошенному адресу в базе данных теперь осуществляется с учётом регистра
1. FIX: ссылки начинающиеся с пробельного символа считаются неправильными
1. FIX: устранение дублирования главной страницы в хлебных крошках в случае входа во вложенную структуру
1. FIX: setPath в случае 404-ой ошибки
1. FIX: ранее введённые значения в поле типа 'Ideal_Price' теперь показываются при открытии формы на редактирование
1. FIX: параметр 'indexedOptions' теперь присутствует в файле настроек по умолчанию, после установки последней версии Ideal CMS

Версия 4.0
---
1. UPD: Twitter Bootstrap до версии 3.3.7
1. FIX: баг с выпадающими списками в CKEditor при открытии его в модальном окне Twitter Bootstrap
1. ADD: возможность указать кол-во элементов в листалке в админской части
1. FIX: админская модель аддона не используется во внешней части сайта

Версия 3.5.10
---
1. FIX: главная страница теперь всегда попадает в список ссылок с заданным приоритетом при формировании отчёта о перелинковке
1. FIX: временный файл отчёта о перелинковке очищается в тех же случаях, что и основной временный файл
1. FIX: отчёт о перелинковке более информативен, а так же отсылается только если произошли какие-нибудь изменения

Версия 3.5.9
---
1. FIX: обработка варианта, когда контент рассматриваемой страницы не в utf-8 для скрипта сбора карты сайта
1. ADD: скрипт карты сайта теперь дополнительно генерирует отчёт о перелинковке
1. FIX: приоритет ссылок теперь верно выставляется в карте сайта в зависимости от настроек
1. FIX: год в копирайте

Версия 3.5.8
---
1. FIX: правила отдачи файлового кэша
1. ADD: по умолчанию для каждой страницы выводится каноническая ссылка

Версия 3.5.7
---
1. FIX: для сообщения с карты сайта оптимизаторам используется другой заголовок, чем с крона
1. ADD: возможность не выводить на печать форму, а вернуть html-код в переменную
1. FIX: отображение метатегов даже если не был установлен номер страницы

Версия 3.5.6
---
1. FIX: правильное определение в листалке страницы без номера

Версия 3.5.5
---
1. FIX: если в параметр номера страницы было передано отрицательное число, то устанавливаем первую страницу и выдаём 404

Версия 3.5.4
---
1. FIX: извлечение ссылок из кода страницы при составлении карты сайта
1. FIX: изменено название параметра в настройках карты сайта
1. FIX: работа с utf-ссылками в карте сайта
1. FIX: уведомление о старой карте сайта приходит не более одного раза за 12 часов
1. FIX: более ровное отображение элементов в аддоне 'Фотогалерея'
1. FIX: правильный путь до папки с кастомными связующими таблицами при проверке целостности базы данных

Версия 3.5.3
---
1. DEL: структура 'Ideal_log' 
1. FIX: результаты работы карты сайта в json-формате отсылаются на специальную почту вместо записи в логи
1. FIX: тег 'base' учитывается при сборе ссылок в скрипте генерации карты сайта
1. FIX: специфическая тема письма для отчётов о работе карты сайта в формате 'json'
1. DEL: убрано подключение структуры Логов по умолчанию после установки
1. FIX: определение наличия ключа при выводе http-заголовков

Версия 3.5.2
---
1. FIX: запуск невыполненных скриптов предыдущих версий раньше скриптов текущей версии 
1. FIX: #332 ошибка при загрузке файла backup на сервер 
1. FIX: отображение нескольких уведомлений об ошибках при загрузке файла на сервер

Версия 3.5.1
---
1. FIX: правильное определение 'prev_structure' при записи в 'Логи' 
1. FIX: при создании карты сайта запись в 'Логи' осуществляется только если что-то изменилось
1. UPD: обновлена библиотека Яндекс.Поиска
1. ADD: возможность использования прокси скрипта для библиотеки Яндекс.Поиска
1. FIX: абстрактная модель аддона 'ЯндексПоиск' переделана для работы с обновлённой библиотекой 'Яндекс.Поиска'
1. FIX: чтение/запись файлов в классе '\Ideal\Structure\Service\SiteData\ConfigPhp' происходит с учётом путей включения

Версия 3.5
---
1. ADD: группы пользователей админки и установка прав для них
1. ADD: логирование событий со скрипта составления карты сайта
1. FIX: определение размера рассматриваемой страницы при сборе карты сайта
1. FIX: продолжение сбора карты сайта при выходе по таймауту и отмеченной галочке 'Сброс ранее собранных страниц'
1. FIX: правильный путь до файла настроек карты сайта
1. FIX: правильные пути до файлов обновлений, которые должны выполнятся после копирования системных файлов

Версия 3.4.2
---
1. FIX: страницы просмотренные администратором больше не сохраняются в файловый кэш
1. FIX: проверка на надобность работы с файлами при отправке формы (FormPhp)

Версия 3.4.1
---
1. ADD: сбор ошибок во время работы FormPHP
1. FIX: оптимальное качество при ресайзе изображений согласно PageSpeed Insights
1. FIX: проверка целостности кастомных аддонов
1. ADD: возможность задать папку, открывающуюся по умолчанию, для CKFinder
1. FIX: правильное удаление аддонов при удалении страницы
1. ADD: возможность редактировать referer в заказах (чтобы создавать новые и править, если неудачные, старые)

Версия 3.4
---
1. FIX: корректное определение неправильных обращений к ajax-контроллеру
1. FIX: упразднён режим ajax-model, теперь и в режиме ajax по url определяется используемая модель
1. FIX: медиум TagsList переименован в TagList
1. Обновление класса минификации JS и CSS - MagicMin до версии 3.0.4
1. FIX: настройка минификации JS для полного удаления коментариев
1. ADD: управление необходимостью отправки заголовков в FormPHP
1. ADD: возможность вмешиваться в результат валидации формы на уровне js
1. ADD: в процессе установки CMS добавляем текст на главную страницу
1. FIX: роутинг Ajax-запросов в админке
1. FIX: в шаблоне по умолчанию футер прижат к низу окна браузера

Версия 3.3.2
---
1. FIX: свойство 'error404' в роутере пишется с маленькой буквы
1. FIX: при кэшировании страниц в файлы больше не используется файл хранящий информацию о всех закэшированных страницах (site_cache.php)
1. FIX: определение наличия вызываемого action и обработка 404ых ошибок должна происходить в Router а не в AjaxController
1. FIX: экранируем все возможные символы в момент преобразования правила игнорирования или известных 404ых к виду регулярного выражения
1. FIX: очистка 'memcache' происходит только тогда, когда включена соответствующая опция в настройках
1. FIX: кэширование запросов к БД может быть включено только при условии доступности класса 'Memcache'
1. FIX: если url не задан, то getUrl возвращает пустую строку
1. FIX: информация в описаниях к картинкам для аддона 'Фотогалерея' теперь сохраняется даже если картинки не были пересортированны
1. FIX: сброс собранных ссылок в карте сайта через 25 часов после последнего сохранения промежуточного файла

Версия 3.3.1
---
1. FIX: совместимость с PHP 5.3 в части использования константы JSON_UNESCAPED_UNICODE

Версия 3.3
---
1. ADD: справочник 'Ошибки 404'
1. ADD: протоколирование 404-ых ошибок в БД
1. ADD: правила для пропуска роутинга для известных 404-ых ошибок
1. FIX: уведомление об авторизованности пользователя в сообщениях об 404-ых ошибках
1. FIX: Сообщение о 404ой ошибке отправляется на почту, только если запрошенная страница не зарегистрирована 
        среди уже известных 404ых
1. FIX: определение 404-ых страниц либо по точному url, либо по регэкспам
1. ADD: в проверке целостности БД проверяется и соответствие полей структуре, описанной в файлах config.php
1. FIX: при построении двухуровнего меню, родительский сегмент в url пропускается, если у него стоит is_skip=1
1. FIX: при установке Справочники отображаются в меню админки, так как там теперь уже есть список 404 и заказы
1. FIX: при сжатии css и js теперь возвращается путь до сжатого файла, вместо его вывода
1. FIX: теперь даже если отключен флеш и включено файловое кэширование происходит попытка получить реферер
1. FIX: для where части sql-запроса необязательно передавать параметры для подстановки
1. FIX: теперь unicode ходит в ajax-запросах без экранирования
1. ADD: возможность переопределить настройки CKFinder файлом ckfinder.php в корне системы
1. FIX: при каждом изменении настроек кеширования сбрасываются файлы all.min.css и all.min.js
1. FIX: при составлении списков js и css-файлов для минификации теперь можно указывать URL, а не только локальные пути
1. ADD: настройка для минификации css и js-кода, генерируемого для формы

Версия 3.2.1
---
1. FIX: теперь по умолчанию SSI выключены, чтобы и на html файлах отдавался Last-Modified
1. ADD: возможность установки аргументов js функции формы
1. FIX: класс 'ConfigPhp' генерирует вкладки для показа только тогда, когда в них есть хотябы одно поле
1. FIX: удаление twig-шаблонов при снятии галок с пунктов 'Кэширование twig-шаблонов'

Версия 3.2
---
1. FIX: все закэшированные страницы хранятся в отдельной папке, а не в куче с остальными файлами
1. FIX: при создании паролей будет использоваться более надёжная функция password_hash, если она доступна
1. FIX: при создании файла кэшируемой страницы происходит установки времени модификации
1. FIX: инициализация метрики и аналитики в скрипте формы выполняется после блокировки повторной отправки формы
1. ADD: ответ полученный от ajax запроса после отправки формы доступен в событиях 'form.successSend' и 'form.errorSend'

Версия 3.1.2
---
1. ADD: валидатор 'required' теперь можно применять и к радиокнопкам
1. FIX: вывод ошибок при неудачной попытке записи в базу во время редактирования элемента в админке
1. ADD: метод добавления несольких строк в БД одним запросом
1. FIX: редиректы теперь можно создавать и с выражениями вида '\w+, \d+'
1. FIX: при создании паролей будет использоваться более надёжная функция password_hash, если она доступна
1. FIX: undefined index cache при сохранении данных, не связанных с изменением данных о кэшировании
1. FIX: при создании нового раздела нет получения привязанных к нему элементов
1. FIX: реферер сохраняется в куках на 10 лет, а не на 100 (для совместимости с PHP 5.3)

Версия 3.1.1
---
1. FIX: перевод в нижний регистр url перед транслитерацией
1. ADD: игнорирование в xml-карте сайта ссылок начинающихся на tel
1. FIX: учет разницы номеров страниц поиска Яндекс.XML и pager'a
1. FIX: определение url для страниц с is_skip на первом уровне
1. ADD: добавление атрибутов формы
1. FIX: отправка уведомлений о работе кары сайта
1. FIX: обработка в Яндекс.XML случаев, когда ничего не найдено

Версия 3.1
---
1. ADD: возможность использования прокси скрипта для ЯндексПоиска
1. ADD: библиотека ЯндексПоиска получает текст ошибки напрямую от Яндекса без дополнительного класса
1. ADD: скрипт обновления для поля 'proxyUrl'
1. ADD: скрипт обновления для поля 'proxyUrl'
1. FIX: более правильное определение папки админки в скрипте локального обновления
1. ADD: вывод графика 'Распределение заказов по видам'
1. FIX: настройки Ideal CMS можно перезагружать из файлов в run-time
1. FIX: построение url при наличии is_skip сегментов
1. FIX: определение страницы с вложенными is_skip структурами
1. FIX: построение ссылок для меню с произвольным количеством вложенностей
1. FIX: скрипт перехода на аддоны
1. ADD: график "Сумма заказов" в раздел Конверсии
1. FIX: в класс View перенесено определение корневых папок для подключения шаблонов
1. FIX: увеличено качество картинки при сжатии
1. FIX: значение по умолчанию для столбца 'addon'
1. ADD: FormPhp: вывод алерта и очистка формы после отправки стали опциональными
1. FIX: пропуск ненужных get-параметров при составлении xml-карты сайта

Версия 3.0.1
---
1. ADD: скрипт обновления, добавляющий структуры справочников и заказов с сайта для сайтов на версии 3.0

Версия 3.0
---
1. Переход с Template на Addon (теперь можно добавить несколько аддонов к одному элементу структуры)
1. ADD: поле Ideal_Template позволяет указать любой twig-шаблон для подключения к каждому элементу
1. ADD: аддон Фотогалерея, позволяющий легко оперировать фотографиями на одной странице
1. ADD: аддон Яндекс.XML для реализации поиска по сайту (перенесён из структуры)

Версия 2.4
---
1. ADD: получение информации о том, откуда пришёл пользователь первый раз
1. ADD: значение реферра сохраняется во флеш куках
1. ADD: структура Ideal_Order для записи заказов с сайта
1. ADD: раздел Конверсии в Сервисе с графиками конверсий
1. ADD: скрипт обновления, добавляющий структуры справочников и заказов с сайта

Версия 2.3.5
---
1. FIX: конвертация кода амперсанда в знак амперсанда при сборе карты сайта
1. FIX: автозагрузка классов FormPhp при использовании его отдельно от фреймворка
1. ADD: виджет для вывода двухуровневого меню
1. ADD: виджет для вывода многоуровневой иерархии страниц

Версия 2.3.4
---
1. ADD: в файл _.php добавлена проверка $isConsole
1. FIX: получение количества скриптов для обновления

Версия 2.3.3
---
1. FIX: неверное определение подключения Google Analytics в FormPhp
1. FIX: вывод сообщений валидатора при одной ошибке в форме FormPhp
1. FIX: пример использования FormPhp

Версия 2.3.2
---
1. FIX: расположение кнопок редактирования и удаления элемента
1. FIX: убрал лишнее уведомление об удалении элемента
1. FIX: уведомление об ошибке при составлении xml-карты сайта, если ссылка заканчивается на пробел
1. ADD: класс для тестирования методов Sitemap\Crawler
1. FIX: в поле Url не сохранялись ссылки на другие страницы
1. FIX: функция получения значения во фреймворке форм была переименована в 'getValue'
1. ADD: во фреймворк форм добавлен метод для отправки сообщений
1. WRN: в FormPhp/Field/FileMulti/Controller метод getFileInputBlock переименован в getInputText
1. ADD: варианты подключения окружения в примере использования фреймворка форм FormPhp
1. ADD: абстрактные методы для получения html-кода меток и полей ввода
1. ADD: пример отправки письма через фреймворк форм
1. ADD: поле Ideal_Price
1. FIX: поле Ideal_Integer - html5-защита от ввода дробных чисел
1. ADD: структура заказов с сайта Ideal_Order
1. ADD: поле Ideal_Referer в ideal CMS
1. ADD: поле Referer во фреймворке форм FormPhp

Версия 2.3.1
---
1. FIX: ошибки в обработке is_skip для вложенных структур
1. FIX: notice при сохранении site_data.php
1. ADD: в библиотеке форм, добавлено срабатывание целей Google Analytics
1. FIX: исключение из html-карты сайта вложенных элементов из скрытых разделов
1. FIX: если у элемента прописан is_skip=1 и url='---', то в html-карте сайта не выводим его url

Версия 2.3
---
1. ADD: полностью переписан скрипт сбора xml-карты сайта
1. ADD: обновление на одну версию возможно локально, через консоль setup/update.php
1. Улучшено обновление модулей из админки
1. FIX: проблема с обращением к админке по любому адресу, начинающемуся с названия админки
1. FIX: проверка существования переменной в $_REQUEST при помощи функции isset()

Версия 2.2
---
1. Реализована проверка целостности скриптов CMS
1. WRN: при создании элементов в админке поля не пустые, а полностью отсутствуют в pageData
1. FIX: нулевые значения для числовых полей в БД
1. ADD: метод для получения номера отображаемой страницы

Версия 2.1.1
---
1. FIX: экшены AjaxController теперь могут возвращать контент, который затем выведет FrontController
1. ADD: подсветка розовым ссылки на главную в шапке админки, если находимся в режиме разработчика
1. FIX: проверка на существование такой страницы, если страница выдаёт не 200 и не 404
1. FIX: проблемы с файловым кэшированием
1. FIX: проверка пустого значения при редактировании поля в админке 
   (теперь число 0 не будет считаться как незаполненное поле)
1. ADD: переменная isAdmin во View, определяющая, залогинен пользователь в админку или нет         

Версия 2.1
---
1. FIX: создание новых элементов при повторном нажатии на кнопку Применить при создании элемента
1. ADD: FormPhp\Select
1. UPD: bootstarp-multiselect
1. Защита от подбора брутфорсом доступа к админке
1. Файловое кэширование (создание статических файлов для страничек, генерируемых из БД)
1. FIX: определение дублированных URL

Версия 2.0
---
1. ADD: класс поля для загрузки файлов в фреймворк FormPhp
1. WARNING!!! В контроллере вьюха не переинициализируется при повторном вызове templateInit(), 
   если она уже была инициализирована
1. ADD: теги Ideal_Tag и подключены к новостям
1. FIX: html-версия письма отправляется в quoted-printable
1. FIX: ошибки в сервисе бэкапа
1. Запрещено создание страниц с одинаковым URL
1. FIX: подсветка полей с ошибками в админке
1. Проверка случая, если в Ideal_Part за найденным элементом с is_skip есть ещё элементы с is_skip
1. ADD: в Site\Model.php метод-заглушка, используемый для построения html-карты сайта

Версия 2.0b17
---
1. ADD: фреймворк FormPhp для работы с формами
1. FIX: работа карты сайта с указанным на странице html-тегом <base>
1. FIX: тема уведомления о 404-ой ошибке заменена на "Страница не найдена (404) на сайте ..."
1. ADD: автопродолжение сбора карты сайта в админке
1. ADD: окно логина после ajax-запроса на сохранение данных
1. FIX: проблема с определением состояния auto url при формировании url по полю отличному от name
1. UPD: пересохранение конфигурационных файлов, чтобы в значениях были двойные кавычки
1. ADD: импорт базы данных через админку
1. ADD: добавление номера версии админки к названию файла бэкапа 
1. ADD: добавление комментариев к файлам бэкапа
1. FIX: приоритеты продвигаемых ссылок при создании карты сайта

Версия 2.0b16
---
1. FIX: сохранение атрибута data-* у тегов в Rich_Edit
2. FIX: при проверке домена для установки опции isProduction теперь не учитывается www
3. ADD: отображение в списке элементов админки значка картинки и отображение всплывающей 
   картинки при наведении на значок
4. FIX: удаление старой временной папки CMS при обновлении
5. ADD: возможность генерировать данные из шаблона в AjaxController
6. ADD: для known404 можно записывать правила в формате htaccess
7. FIX: работа с переводами строки при редактировании конфигов через админку

Версия 2.0b15
---
1. ADD: возможность указывать по какому полю генерировать url
2. FIX: в файле бэкапа базы таблицы дропаются перед созданием
3. Обновление Twig до версии 1.16.3
4. ADD: resize для png-файлов
5. FIX: в конфиге значение параметра может быть окружено как одинарными кавычками, 
   так и двойными, а сохраняет только двойными
6. ADD: отправка писем о битых ссылках, за исключением $config->cms['known404']

Версия 2.0b14
---
1. FIX: отображение is_skip страниц
2. FIX: правильное определение URL, когда один из элементов пути - ссылка
3. FIX: обработка случая, когда по одному url есть несколько новостей
4. FIX: указание номера страницы в title
5. __FIX: по умолчанию номер страницы равен 1, а если идёт запрос списка страниц, то номер страницы будет null__
6. FIX: создание файла update.log
7. FIX: отображение multiselect
8. FIX: пропуск незаполненных sql-полей при создании таблицы

Версия 2.0b13
---
0. __ВАЖНО: Изменено название метода Util::is_email на Util::isEmail !!!__
1. В скрипте отправки писем сделана возможность указывать только html-код письма, без plain-версии
2. Чтобы не накручивать статистику Метрики и Аналитики добавлена возможность определения места выполнения скрипта
(production/development)
3. Обновление кода FirePHP до самого актуального
4. FIX: копирование минифайеров при установке CMS
5. ADD: возможность указания в .htaccess логина, пароля и названия базы данных
6. CKEditor обновлён до версии 4.4.6
7. FIX: выдача 404 ошибки на неправильно сформированный параметр action в query_string

Версия 2.0b12
---
1. Обновлён скрипт изменения размера изображения
2. Тег \<style\> теперь можно использовать в визуальном редакторе текста
3. Свойство sqlAdd должно быть инициализировано для каждого редактируемого поля
4. Indirect modification массивов в классе View
5. FIX: неправильные иконки в CKEditor
6. ADD: метод finishMod в Helper для финальных модификаций в тексте страницы

Версия 2.0b11
---
1. Улучшен внешний вид редактирования поля SelectMulti
2. ADD: правило в .htaccess для создания картинок с изменёнными размерами
3. ADD: суффикс тайтла для листалки
4. FIX: карта сайта не будет создаваться, если не были собраны ссылки
5. FIX: принудительное создание карты в админке
6. FIX: проблема с разбором site_data, при наличии символа табуляции вместо пробелов
7. FIX: проблемы связанные с обновлением системы

Версия 2.0b10
---
1. FIX: название файла с классом минификатора в генераторах минифицированных файлов
2. FIX: гарантированная установка body в классе отправки почты
3. FIX: подключение js-файла локализации для DateTimePicker
4. FIX: не убирать из RichEdit пустые span и span с классами
5. FIX: возврат к версии CKEditor 4.4.4, так как в 4.4.5 не работает CodeMirror

Версия 2.0b9
---
1. Исправлено некорректное формирование url у новостей
2. Удалена типизация в методе Core\AjaxController::run, так как теперь там может быть и Site и Admin
3. Исправлена генерация капчи на новых версиях PHP
4. Обновлены библиотеки Moment.js и bootstrap-datatime-picker для корректной работы в Chrome

Версия 2.0b8
---
1. Усовершенствована система обновлений:
2. Каждый этап обновления происходит с помощью отдельного ajax-запроса
3. Скрипты обновления разделены на две части: работающие до обновления CMS и работающие после обновления CMS
4. Добавлен метод рекурсивной смены прав для папок и файлов

Версия 2.0b7
---

1. FIX: удаление в админке элементов ростера и пользователей
2. FIX: дублирование слэшей в поле Area
3. Изменение схемы вызова ajax-контроллеров
4. Создание файла настроек site_map.php в корне админки, если его нет в системе
5. Подключение twig-шаблонов внутри самих шаблонов с помощью указания пути к шаблону от корня админки
6. CKFinder обновлён до версии 2.4.2
7. Twitter Bootstrap обновлён до версии 3.2.0
8. Переход на версию JQuery 2.1.1 (в админке не поддерживаются IE 6, 7, 8)
9. CKEditor обновлён до версии 4.4.5
10. Добавлен объединитель и минимизатор JS и CSS файлов
11. FIX: система обновлений

Версия 2.0b6
---
1. FIX: если не определён mysqli_result::fetch_all (не подключён mysqlnd)
2. Изменена структура файла site_data.php:
3. Поля startUrl, errorLog выведены во вкладку cms
4. Поле tmpDir перенесено во вкладку cms и переименовано в tmpFolder
5. Удалено поле templateCachePath
6. Поля isTemplateCache и isTemplateAdminCache переименованы в templateSite и templateAdmin и перенесены во вкладку cache
7. Во вкладку cache добавлено поле memcache

Версия 2.0b5
---
1. Вкладки в окне редактирования перенесены в заголовок
2. FIX: в CKEditor удалялся тег script и атрибуты style и class
3. Отображение страниц с is_skip=1
4. FIX: формат конфигурационного файла в папке установки
5. FIX: постраничная навигация, лог ошибок в файл, удаление элементов в админке

Версия 2.0b4
---
1. При обновлении CMS и модулей могут выполнятся php и sql скрипты
2. Внедрение нового класса доступа к БД, расширяющего mysqli и с кэшированием через memcached
3. Завершение перевода работы с картой сайта через админку

Версия 2.0b3
---
1. Обновление CKEditor до версии 4.4.3 и удаление нескольких неиспользуемых модулей
2. При обычном подключении RichEdit появляются ВСЕ кнопки
3. Мелкие правки для устранения notice и warning сообщений

Версия 2.0b2
---
1. Показ миниатюры картинки для поля Ideal_Image
2. Добавлена новая сущность Medium
3. Обновлён FirePHP
4. Добавлено поле Ideal_SelectMulti
5. Исправления в карте сайта (обработка ссылок tel, многострочных html-комментариев)
6. Исправлена страница установки CMS для работы под Twi Bootstrap 3 и сделана двухколоночная вёрстка
7. Регулярные выражения для исключения URL в html-карте сайта
8. Исправлена отправка писем с разными типами вложений
9. Работа с картой сайта через админку
10. Исправлена проблема с экранированием слэшей и кавычек в Ideal_Area
11. Обновление CKEditor до версии 4.4.2
12. Отображение на сайте скрытой страницы для авторизированных в админку пользователей

Версия 2.0c
---
1. Обновление jquery-плагина datetimepicker до версии 3.0
2. FIX: определение кол-ва элементов на странице
3. FIX: проверка наличия кастомных и модульных папок Template в виде таблиц в базе
4. FIX: размер модального окна в админке при изменении размера окна браузера
5. FIX: получение default значения
6. __ADD: Новый тип поля Ideal_Integer__
7. FIX: фильтр для toolbar в админке
8. Новая вёрстка шаблона front-end под Twitter Bootstrap 3

Версия 2.0b
---
1. FIX: листалка в админке в стиле Twi 3
2. FIX: доработка редактирования редиректов под Twi 3
3. FIX: доработка создания резервных копий БД под Twi 3
4. Обновление Twitter Bootstrap до версии 3.1.1
5. FIX: Исправлена проблема с автоматической генерацией url
6. ADD: вкладки в настройках в админке

Версия 2.0a
---
1. __Обновление Twitter Bootstrap до версии 3__
2. Изменения в админской части для перехода на Bootstrap 3

Переход на версию 1.0
---

1. Во всех структурах поле structure_path изменено на prev_structure и содержит
ID родительской структуры и ID родительского элемента в этой структуре.

2. Изменён принцип роутинга. Теперь для вложенных структур метод detectPageByUrl
вызывается не из роутера, а из родительской структуры. Что даёт возможность
правильно обрабатывать вложенный структуры с элементами is_skip.

3. Изменён корневой .htacces, теперь адрес страницы не передаётся в GET-переменной,
а берётся в роутере из `$_SERVER['REQUEST_URI']`.

4. Переменная модели object переименована в pageData и сделана protected, а также
переименованы соответствующие методы.

5. Определение 404-ошибки перенесено из роутера в методы detectPageBy* модели.
В этих методах должны инициализироваться свойства класса path и is404, а сами
методы возвращают либо свой объект (`$this`), либо объект вложенной модели. Для
404 ошибки добавлен специальный шаблон 404.twig и экшен error404Action в контроллерах.