# Kirw Test People Block

## Использование плагина:
Для того, чтобы импортировать данные из вложенного файла people.csv, нужно на фронтенде добавить ?csv_people_import=1
Это запустит импорт текстовых данных. Затем фото из Википедии будут подружаться по заданию wp-cron по 30 штук каждые 5 минут.
Даже если до 30 оборвется по таймауту, позже будет продолжено с того же места, т.к. результат сохраняется.
Далее в Gutenberg нужно добавить People Block и выбрать нужных людей для отображения. 
