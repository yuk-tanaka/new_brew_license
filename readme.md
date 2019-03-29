# new_brew_license

## 概要
- 新規開業する酒蔵・ブルワリーなどを見つけたいという試み
- [国税庁](https://www.nta.go.jp/taxes/sake/menkyo/shinki/seizo/02.htm) より新規酒造免許取得者の名簿をスクレイピングする
- スクレイピング結果のうち、「新規開業」のデータをDB保存＆LINE通知
- お気持ち程度のAPI

## setup
1. ```composer install```
1. [Line Notify](https://notify-bot.line.me/ja/) からtokenを取得し、`.env` に記載
1. ```php artisan migrate --seed```
1. [cronエントリを追加](https://readouble.com/laravel/5.8/ja/scheduling.html) ※Laravelと同一の手順

## .env
- LINE_NOTIFICATION_KEY string Line Notifyトークン
- CAN_SEND_NOTIFICATION bool Lineに通知を送信するかどうか 
- API_PAGINATION int 1回のAPIリクエストで取得できる最大件数

## API

### Licenses
- GET /licenses
- GET /licenses/id 
- GET /licenses/search
    - drink_type_id integer
    - prefecture integer
    - name string LIKE検索
    - address string LIKE検索
    - start date
    - end date 許可日start~end期間

### DrinkTypes
- GET /drinkTypes

















