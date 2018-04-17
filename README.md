# httpserver-php

a http server writed by php which support several network models.

## flow

```

start -> read config -> initialize -> fork process -> read and parse request 
-> process request -> cgi/response -> write log

``` 

## todo

- [ ] multi process model
- [ ] multi thread model
- [ ] epoll/kqueue model
- [ ] http parser
- [ ] reator model
- [ ] fastcgi protocol
- [ ] load balance
- [ ] cache response
- [ ] logger
- [ ] https support