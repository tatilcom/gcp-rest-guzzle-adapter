### Simple Guzzle adapter to Google Cloud Platform REST APIs

##### Use Case
Accessing to Google Cloud Platform Rest APIs using Service Account Credentials (Google's recommended way) 

For more information about authentication: https://cloud.google.com/speech/docs/common/auth

#### Usage 

```php
use \GcpRestGuzzleAdapter\Client\ClientFactory;

// Service Account Email
$email = 'pubsub@test-project123.iam.gserviceaccount.com';

// Private Key
$key = '-----BEGIN PRIVATE KEY-----SDADAavaf...-----END PRIVATE KEY-----';

// Scope of Google Cloud Service
$scope = 'https://www.googleapis.com/auth/pubsub';

// Full base url of project
$projectBaseUrl = 'https://pubsub.googleapis.com/v1/projects/test-project123/';

$pubSubClient = ClientFactory::createClient($email, $key, $scope, $projectBaseUrl);

$result = $pubSubClient->get(
    sprintf('topics/%s/subscriptions', 'test-topic')
);

var_dump((string)$result->getBody()->getContents());

```

###### Result
```php
string(113) "{
  "subscriptions": [
    "projects/test-project123/subscriptions/test_topicSubscriber"
  ]
}
"
```

#### Requirements
- php >=5.6
- guzzle 5.3
- firebase/php-jwt 4.0
- apc or apcu for token caching (custom handler also injectable)