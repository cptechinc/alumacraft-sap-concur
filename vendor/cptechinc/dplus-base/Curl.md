# Curl 

## Requests
### Send Requests Without Authentication
Steps Needed for OAuth2 cURL Requests
1. Create Curl Class <br>
    `$curl = new Curl();`
2. Add Accept Types  **NOTE: Will add multiple Accept Types** <br>
    `$curl->add_acceptheader('json');`
2. Set Content Type <br>
    `$curl->set_contenttype('json');`
5. Send Request <br>
    `$curl->get($url) | $curl->post($url, $body) | $curl->put($url, $body)`
    
### Send OAuth2 Requests
Steps Needed for OAuth2 cURL Requests
1. Create Curl Class <br>
    `$curl = new Curl();`
2. Add Accept Types  **NOTE: Will add multiple Accept Types** <br>
    `$curl->add_acceptheader('json');`
2. Set Content Type <br>
    `$curl->set_contenttype('json');`
3. Set Authentication Type  <br>
    `$curl->set_authentication('oauth2');`
4. Set the Access Token <br>
    `$curl->authentication->set_accesstoken($token)`
5. Send Request <br>
    `$curl->get($url) | $curl->post($url, $body) | $curl->put($url, $body)`

## Response
### cURL Success
```
    {
        'server': {
            'error' : false,
            'message': '',
            'http_code': 200
        }, 
        'response': {
            // CURL RESPONSE 
        }
    }
```

### cURL Failure
```
    {
        'server': {
            'error' : false,
            'message': 'Curl Message',
            'http_code': 404
        }, 
        'response': false
    }
```
