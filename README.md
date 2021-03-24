## Important 
##### This repository serves as the backend of [this](https://github.com/prantaDutta/final-frontend) project.

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
```

### Step 3
- Update the env file with your database credentials

### Step 4
 - Generate a Key. Run the following: 
 ```$xslt
    php artisan key:generate
```

### Step 5
 - Migrate the database
 ```$xslt
    php artisan migrate
```

### Step 6
 - Now we need to add this to your env file
 ```$xslt
    # for laravel sanctum
    SANCTUM_STATEFUL_DOMAINS=localhost:3000
    SESSION_DOMAIN=localhost
    
    # To Redirect To Login
    FRONT_END_URL=http://localhost:3000
    
    # SSLCommerz credentials
    STORE_ID= // this is secret, lol
    STORE_PASSWORD=  // sry, no can do
```
 - Also Change the laravel queue connection to database
```angular2html
    # for laravel queue
    QUEUE_CONNECTION=database
```

### Step 7
  - Now Run the development server
```$xslt
    php artisan serve
```   
 
