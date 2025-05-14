# COACHTECH 勤怠管理アプリ

## 概要
従業員の出退勤・休憩・勤怠修正申請を管理するWebアプリケーションです。  
一般ユーザーは出勤・退勤・休憩開始・休憩終了の記録、月次の勤怠一覧参照、勤怠詳細確認、誤打刻時の修正申請ができます。  
管理者はユーザーごとの勤怠一覧参照、CSV出力、修正申請の承認・反映が行えます。

## 主な機能
- **一般ユーザー**
  - 出勤／退勤打刻
  - 休憩開始／休憩終了打刻
  - 月次勤怠一覧の閲覧
  - 日別勤怠詳細の閲覧
  - 勤怠修正申請の送信
- **管理者**
  - 全ユーザーの勤怠一覧参照
  - ユーザー別月次勤怠一覧参照・CSV出力
  - 勤怠修正申請の一覧確認・承認

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:H1g4s4/git@github.com:H1g4s4/Attendance-management.git
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
php artisan storage:link
```

## 使用技術

- **PHP**:8.83.8
- **Laravel**:7.4.9
- **MySQL**:10.3.39

## テーブル設計
<img width="594" alt="スクリーンショット 2025-05-14 20 06 23" src="https://github.com/user-attachments/assets/af3d6c74-99d9-448a-b7eb-d37b944442e5" />
<img width="594" alt="スクリーンショット 2025-05-14 20 06 38" src="https://github.com/user-attachments/assets/95b6abbc-c02d-4748-8cc0-8f571f56a612" />
<img width="594" alt="スクリーンショット 2025-05-14 20 06 54" src="https://github.com/user-attachments/assets/daececd3-668d-4141-b6ce-8356b3b13806" />
<img width="594" alt="スクリーンショット 2025-05-14 20 07 08" src="https://github.com/user-attachments/assets/1fba03d9-4084-4716-98de-3ac6d907b58a" />
<img width="594" alt="スクリーンショット 2025-05-14 20 07 18" src="https://github.com/user-attachments/assets/24283ac3-ea96-473c-a02f-64c1a06b8265" />

## ER図
<img width="645" alt="スクリーンショット 2025-05-14 20 11 48" src="https://github.com/user-attachments/assets/a26315c2-fb11-481b-bbd5-83c13632ddc5" />

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
