# httpserver-php

a http server writed by php which support several network models.

## flow

```

start -> read config -> initialize -> fork process -> read and parse request 
-> process request -> cgi/response -> write log

``` 

## todo

- [X] multi process model
- [X] multi thread model
- [X] select/poll model
- [X] epoll/kqueue model
- [ ] reator model
- [X] http parser
- [ ] fastcgi protocol
- [ ] load balance
- [ ] cache response
- [X] logger
- [ ] https support
- [ ] http 2.0 protocol
- [ ] multi language 