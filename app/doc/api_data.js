define({ "api": [
  {
    "type": "get",
    "url": "/user/author?login=12334545&password=******",
    "title": "Авторизация",
    "version": "1.0.0",
    "name": "author",
    "group": "User_API",
    "description": "<p>Метод возвращает данные по авторизованому пользователю.<br> Поддерживает способы передачи: GET/POST (пример для GET)<br> * — поля, обязательные для заполнения</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "login",
            "description": "<p>Логин* учётной записи</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Пароль* учётной записи</p>"
          }
        ]
      }
    },
    "filename": "./user/author.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/author?login=12334545&password=******"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/get_user?token=***&user_id=**",
    "title": "Получение пользователя по ID",
    "version": "1.0.0",
    "name": "get_user",
    "group": "User_API",
    "description": "<p>Метод возвращает данные по авторизованому пользователю.<br> Поддерживает способы передачи: GET/POST (пример для GET)</p>",
    "parameter": {
      "examples": [
        {
          "title": "Пример POST:",
          "content": "/app/user/get_user/\nPOST: \ntoken=[value]\nuser_id=[value]",
          "type": "post"
        },
        {
          "title": "Пример GET: ",
          "content": "/app/user/get_user?token=***&user_id=***",
          "type": "get"
        }
      ],
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Токен пользователя</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_id",
            "description": "<p>ID пользователя</p>"
          }
        ]
      }
    },
    "filename": "./user/get_user.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/get_user?token=***&user_id=**"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/is_logged?token=******",
    "title": "Проверка авторизации",
    "version": "1.0.0",
    "name": "is_logged",
    "group": "User_API",
    "description": "<p>Проверяет, авторизован ли пользователь, если нет - авторизует по токену, возвращает данные пользователя</p> <p>Технически, проводится в каждом запросе, который требует авторизации. При передаче правильного токена логинит автоматом.</p> <p>Поддерживает способы передачи: GET/POST (пример для GET)</p>",
    "parameter": {
      "examples": [
        {
          "title": "Пример POST:",
          "content": "/app/user/is_logged/\nPOST: token=[value]",
          "type": "post"
        },
        {
          "title": "Пример GET: ",
          "content": "/app/user/is_logged?token=***",
          "type": "get"
        }
      ],
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Токен пользователя</p>"
          }
        ]
      }
    },
    "filename": "./user/is_logged.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/is_logged?token=******"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/logout?token=******",
    "title": "Выход (разлогинивание)",
    "version": "1.0.0",
    "name": "logout",
    "group": "User_API",
    "description": "<p>Разлогинивает авторизованного пользователя</p> <p>Поддерживает способы передачи: GET/POST (пример для GET)</p>",
    "parameter": {
      "examples": [
        {
          "title": "Пример POST:",
          "content": "/app/user/logout/\nPOST: token=[value]",
          "type": "post"
        },
        {
          "title": "Пример GET: ",
          "content": "/app/user/logout?token=***",
          "type": "get"
        }
      ],
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Токен пользователя</p>"
          }
        ]
      }
    },
    "filename": "./user/logout.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/logout?token=******"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/reg/",
    "title": "Регистрация",
    "version": "1.0.0",
    "name": "reg",
    "group": "User_API",
    "description": "<p>Регистрирует пользователя. В качестве логина используется очищенный от артефактов номер телефона Поддерживает способы передачи: POST</p> <ul> <li>— поля, обязательные для заполнения</li> </ul>",
    "parameter": {
      "examples": [
        {
          "title": "Пример POST:",
          "content": "/app/user/reg/\nPOST: \nlogin = [string]\npassword = [string]\t\nemail = [string]\nname = [string]\t\nlast_name = [string]\t\ncity = [string]\nbirthday = [string]\ngender = [string]\nis_rate = [string]\navatar = [file]",
          "type": "post"
        }
      ],
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "login",
            "description": "<p>Логин*  Он же - номер телефона. Можно вводить в произвольном формате(+7 или 8, скобочки, тире, пробелы и пр.), значение имеет только правильный набор цифр</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Пароль*</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Электронный адрес*</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Имя *</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "last_name",
            "description": "<p>Фамилия *</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "city",
            "description": "<p>Город</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "birthday",
            "description": "<p>День рождения</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "gender",
            "description": "<p>Пол [M - мужчина; F - женщина]</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "is_rate",
            "description": "<p>Согласие на участие в рейтингах [Y - да, N - нет]</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": false,
            "field": "avatar",
            "description": "<p>Аватарка</p>"
          }
        ]
      }
    },
    "filename": "./user/reg.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/reg/"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/update/",
    "title": "Изменения профиля пользователя",
    "version": "1.0.0",
    "name": "update",
    "group": "User_API",
    "description": "<p>Изменяет данные профиля пользователя. В качестве логина используется очищенный от артефактов номер телефона</p> <p>Поддерживает способы передачи: POST</p> <p>*- поля, обязательные для заполнения</p>",
    "parameter": {
      "examples": [
        {
          "title": "Пример POST:",
          "content": "/app/user/update/\nPOST: \nlogin = [string]\npassword = [string]\t\nemail = [string]\nname = [string]\t\nlast_name = [string]\t\ncity = [string]\nbirthday = [string]\ngender = [string]\nis_rate = [string]\navatar = [file]",
          "type": "post"
        }
      ],
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Токен пользователя</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "login",
            "description": "<p>Логин*  Он же - номер телефона. Можно вводить в произвольном формате(+7 или 8, скобочки, тире, пробелы и пр.), значение имеет только правильный набор цифр</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Пароль*</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Электронный адрес*</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Имя *</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "last_name",
            "description": "<p>Фамилия *</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "city",
            "description": "<p>Город</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "birthday",
            "description": "<p>День рождения</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "gender",
            "description": "<p>Пол [M - мужчина; F - женщина]</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "is_rate",
            "description": "<p>Согласие на участие в рейтингах [Y - да, N - нет]</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": false,
            "field": "avatar",
            "description": "<p>Аватарка</p>"
          }
        ]
      }
    },
    "filename": "./user/update.php",
    "groupTitle": "User_API",
    "sampleRequest": [
      {
        "url": "http://magicmirror-v2.techmas.ru/app/user/update/"
      }
    ]
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "c__works_Techmas_magic_mirror_magicMirrorBackEnd_app_doc_main_js",
    "groupTitle": "c__works_Techmas_magic_mirror_magicMirrorBackEnd_app_doc_main_js",
    "name": ""
  }
] });
