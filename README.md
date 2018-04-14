# httpserver-php

a http server writed by php which support several network models.

## flow

```

start -> read config -> initialize -> fork process -> read request 
-> process request -> cgi/response -> write log

``` 

## todo

- [ ] http protocol structure
- [ ] multi process model
- [ ] multi thread model
- [ ] reactor model
- [ ] fastcgi protocol
- [ ] load balance