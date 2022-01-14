# Бандл рендеринга статических страниц из роутов для связки Bitrix-Symfony

#### Зачем?    

На практике использую его на проектах, где надо оградить те или иные статические страницы
от возможности редактирования ручками контент-менеджеров через админку.

Т.е. это частное решение весьма локальной задачи.

#### Установка 

1) `composer require proklung/facade.bundle`

2) Активировать бандл в bundles.php (или в случае кастомных использований Symfony - в standalone_bundles.php).

В качестве базы используется форкнутый класс Symfony\Bundle\FrameworkBundle\Controller\TemplateController

### Описание роута (в файле типа routes.yaml)

```yaml
simple_static_page:
  path: /simple_page/
  methods: [GET]
  controller: 'Prokl\StaticPageMakerBundle\Services\TemplateControllerContainerAware::templateAction'
  defaults:
    # Шаблон
    template:  './static/example2.twig'
    statusCode: 202 # HTTP Status = Accepted
    # Контекст, передаваемый Твигу для рендеринга страницы.
    context:
      # Препроцессоры контекста.
      _processors:
        - '@Prokl\StaticPageMakerBundle\Services\ContextProcessors\ExampleContextProcessor'
      options: '@app.options' # Сервис как параметр
      site_name: 'Example site'
      object: # Объект
        errored: true
        finished: true
        pending: true
      page_type: 'static-page' # Битриксовое свойство страницы page_type
      title: 'Простая статическая страница'
      description: 'Простая статическая страница'

```

### Нюансы

1) Перед передачей данных в Твиг существует возможность пропустить данные через цепочку
"препроцессоров", чтобы как-нибудь их автоматически изменить.
    
   Препроцессоры, уже имеющиеся в пакете:
   
    - **SeoContextProcessor** - установка title & description из мета-свойств элементов инфоблока.
       ID (отдельного) инфоблока задается на уровне конфигурации бандла. Ключ в конфиге - seo_iblock_id.
       По умолчанию равен null. 
       
       Формат записи в "SEO" инфоблоке: NAME => url "статической" страницы. Параметры во вкладе SEO.
       
       Даже если эксплицитно указаны параметры title - description в роуте, то подключенный процессор
       "перебьет" их значения (если найдет запись в инфоблоке).
    
       Препроцессор - класс, реализующий интерфейс ContextProcessorInterface с двумя методами setContext
       (сеттер; чтобы не копипастить его каждый раз существует абстрактный класс AbstractContextProcessor) и
       handle (где осуществляется вся работа). Метод должен вернуть измененный (или неизмененный) контекст.
        
       Чтобы препроцессор заработал он должен быть сервисом, помеченным тэгом static_page_maker.default.context.processor.
        
       Препроцессор пускается автоматически, если в конфиге бандла задан ID SEO инфоблока.
    
    - **BreadcrumbsContextProcessor**. Хлебные крошки.
    
       Предполагается, что битриксовый компонент подключается в шапке. Но не обязательно, вполне сработает конструкция
        `{{ showComponent('bitrix:breadcrumbs', '') }}`  
       
       Этот препроцессор пускается последним. В качестве url использует адрес роута, а описание берет из поля title контекста. 

2) При установке опции set_last_modified_header равной true в конфигурации бандла (local/configs/packages/static_page_maker.yaml)
   будут установлены заголовки LastModified. 
   
   Пример конфига модуля лежит по адресу Examples/static_page_maker.yaml относительно корня бандла. 
 
3) В Твиг добавлены две кастомные функции: header и footer, отвечающие за вывод битриксовой шапки и футера. 

   А также функции add_css, add_js и add_string, являющиеся оберткой над соответствующими методами класса Asset.
         
4) Пример шаблона:   

    Базовый (для копипасты лежит в vendor\ProklUng\StaticPageMakerBundle\Examples\templates\static\template\baseTemplate.twig):
    
    ```twig
    {# head #}
    {% block header %}
        {{ header() }}
    
        {{ APPLICATION.SetTitle(title) }}
        {{ APPLICATION.SetPageProperty('description', description) }}
        {{ APPLICATION.SetPageProperty('page_type', page_type) }}
    
    {% endblock %}
    
    {# content #}
    {% block content %}
    
    {% endblock %}
    
    {# footer #}
    {% block footer %}
        {{ footer() }}
    {% endblock %} 
   ```      
   
   Конкретная страница:
   
       ```twig
        {% extends "./static/template/baseTemplate.twig" %}
        
        {# content #}
        {% block content %}
            {{ add_css('/assets/examples/style.css') }}
        
            {% apply inline_css %}
                <br><br><br><br><br>
                <h1>Я статическая страница</h1>
                {{ dump (options) }}
                <div class="test">
                    Мои параметры: {{ site_name }}
                </div>
        
                <div class="test_class">Text</div>
        
                <style>
                    .test {
                        color: red;
                    }
                    .errored {color: rosybrown}
                </style>
        
                {{ encore_entry_link_tags('main') }}
        
                <div class="home-philosophy my-3 my-md-4 my-lg-5 my-xl-10">
                    {{ showComponent('guta:example', '') }}
                    {{ url('foo_route') }}
                </div>
               {% endapply %}
        {% endblock %}
      ```  
   5) Сами шаблоны ищутся согласно зарегистрированным в Твиге путям. В моем случае - в local/twig.

   6) В Твиг подтягиваются все зарегистрированные в системе функции и фильтры.
