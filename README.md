
## Задача 3 

Написать счетчик посещений страницы. Решение должно состоять из двух компонентов:
-кода на js, который подключается к любому сайту. Скрипт должен собрать необходимые данные(ip, город, устройство) и отправлять на сервер
-бэк часть, который хранит данные в БД(sqllite или другой на выбор) и показывает график посещений по часам(по оси х - количество уникальных посещений за час, по оси y- время), круговую диаграмму с разбиением по городам.

## Код на js

```javascriptwindow.addEventListener('DOMContentLoaded', function() {

    const sendVisitStatistic = (site) => {
        /*if(sessionStorage.getItem('isVisit') !== null) {
            return;
        }*/
        fetch('http://194.67.113.12:8585/api/visit_statistic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({site: site}),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data?.success !== undefined && data.success === true) {
                    sessionStorage.setItem('isVisit', true);
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }

    //sendVisitStatistic('имя или адрес сайта');
});
```
