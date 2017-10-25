## Building a faktory Docker image

```
git clone faktory...blah faktory
cd faktory
docker build --build-arg=GOLANG_VERSION=1.9.1 --build-arg=ROCKSDB_VERSION=5.7.3 -t faktory .
```
