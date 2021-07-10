## Docker容器操作

+ 交叉构建多架构镜像

```
shell> docker buildx build -t dnomd343/echoip --platform="linux/amd64,linux/arm64,linux/386,linux/arm/v7" https://github.com/dnomd343/echoIP.git#master --push
```

+ 制作echoIP镜像

```
shell> docker build -t echoip https://github.com/dnomd343/echoIP.git#master
```

+ 启动容器

```
shell> docker run -d --name echoip -p 1601:1601 echoip
```

+ 进入容器调试

```
shell> docker exec -it echoip sh
```
