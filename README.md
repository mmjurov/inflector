# zhmi/inflector

Библиотека, которая позволяет склонять слова с помощью различных инструментов. В качестве базового инструмента для склонения используется [phpMorphy](http://phpmorphy.sourceforge.net/dokuwiki/)

Интерфейс работы простой: 
Инициализируем сервис склонения. Передаем ему кодировку своего проекта, чтобы он знал о том, в какой кодировке к нему будут приходить слова. По-умолчанию utf-8. Словари есть только для utf-8 и windows-1251, поэтому поддерживаются только эти кодировки.
```php
$inflectorService = new \Zhmi\Inflector\Service('utf-8');
```
Выполняем склонение нужного слова
```php
$inflections = $inflectorService->inflect('Дружба');
```

Результат будет получен в виде экземпляра InflectionResult, который позволяет работать с ним как с массивом. Например, если вы хотите получить Родительный падеж, то можно достать его из объекта так:
```php
//Любой из этих вариантов будет верным
echo $inflections[1];
echo $inflections['родительный'];
echo $inflections['genitive'];
echo $inflections->getInflection(1);
echo $inflections->getInflection('родительный');
echo $inflections->getInflection('genitive');
echo $inflections->getGenitive();
```

Если слово не удалось склонить, то будет возвращен экземпляр EmptyInflectionInterface, который при попытке достать склонение будет возвращать исходный вариант без склонения.