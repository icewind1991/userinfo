# UserInfo

API to retrieve user information

## Usage

### List all users

`GET https://cloud.example.com/apps/userinfo/users`
 
```json
{ 
  "icewind" : { 
      "displayname" : "icewind",
      "enabled" : true,
      "last_login" : "2017-04-12T13:39:11+00:00",
      "quota" : "none",
      "total_space" : "7993521505",
      "used_quota" : 846521744
    },
  "test" : { 
      "displayname" : "test",
      "enabled" : true,
      "last_login" : "2017-04-12T12:30:27+00:00",
      "quota" : "10 GB",
      "total_space" : "200324091",
      "used_quota" : 2329733
    }
}
```

### List all users in a group

`GET https://cloud.example.com/apps/userinfo/groups/admin`

```json
{ 
  "icewind" : { 
      "displayname" : "icewind",
      "enabled" : true,
      "last_login" : "2017-04-12T13:39:11+00:00",
      "quota" : "none",
      "total_space" : "7993521505",
      "used_quota" : 846521744
    }
}
```
