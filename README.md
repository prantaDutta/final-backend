# About this file

This is the project repository of our final Project.

# Important

> We are using nextjs as the frontend for this project.
> Download frontend repository from [here](https://github.com/prantaDutta/final-backend)

### Step 1
- First Install the Composer packages
```$xslt
    composer install
```

### Step 2
- Rename .env.example to .env
- You can also do that with following command 
```$xslt
    copy .env.example .env
    // OR
    cp .env.example .env
```

### Step 3
- Update the env file with your database credentials
- Mine is the following
```angular2html
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=grayscale # your database name
    DB_USERNAME=root # database user
    DB_PASSWORD=password # database password
```

### Step 4
 - Generate a Key for Laravel. Run the following: 
 ```$xslt
    php artisan key:generate
```

### Step 5
 - Migrate the database
 ```$xslt
    php artisan migrate
```
 - You can also seed the database to have some data in your database. Just Run
```angular2html
    php artisan db:seed
```
 - You will have some user and admin in your database
```angular2html
    --- user
    email: pranta@gmail.com
    email: priosa@gmail.com
    email: tajree@gmail.com
    pass: 12345678
    --- admin
    email: admin@grayscale.com
    pass: 12345678
```
 - You can sign in with these credentials.

### Step 6
 - Now we need to add this to your env file
 ```$xslt
    # for laravel sanctum
    SANCTUM_STATEFUL_DOMAINS=localhost:3000
    SESSION_DOMAIN=localhost
    
    # To Redirect To Login
    FRONT_END_URL=http://localhost:3000
    
```
- Also Change the laravel queue connection to database. This will help us process loan distribution in a queue.
```angular2html
    # for laravel queue
    QUEUE_CONNECTION=database
```

### Step 7

- Add this line to your .env file.

```angular2html
    # SSLCommerz credentials
    STORE_ID= // this is secret
    STORE_PASSWORD=  // I can't give you mine
```
- If you want sslcommerz credentials, go to [this](https://developer.sslcommerz.com/registration/) link and create a sandbox account. They will send you the credentials to your email.

### Step 8
- Go to [mailtrap](https://mailtrap.io) and create an account. Go to your inbox and you will find your credentials
- Then add them in the env file

```angular2html
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME= // Your username
    MAIL_PASSWORD= // Your password
    MAIL_ENCRYPTION=tls
```

### Step 9
  - Now Run the development server
```$xslt
    php artisan serve
```   
 
### Step 10

  - For any query, email us at prantadutta1997@gmail.com
