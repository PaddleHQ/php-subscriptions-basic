# Paddle Subscriptions Example Implementation

This repo contains an example database table and php code for interpreting webhook responses. They will help illustrate the basics of how subscriptions work and how you can use them within your applications.

We use one table in this example called 'subscriptions’, the structure of which can be found in the `subscriptions.sql` file.

Full subscription documentation, as well as an in-depth integration guide can be found [here](https://www.paddle.com/docs/subscriptions/integration-guide)

## Getting Started

You'll need to execute the `table create` syntax from within the `subscriptions.sql` file into a MySQL database of your choosing:

```shell
mysql -u username -p database_name < subscriptions.sql
```

Then update the database connection information from the top of the `hook.php` file:

```php
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
```

Next, point the subscription alerts from your Paddle account to notify the `hook.php` file on your server. To do this, navigate to the [vendor settings](https://vendors.paddle.com/account) area of your Paddle Dashboard and click the 'Alerts' tab.

On this page you'll want to subscribe to the following alerts via 'webhook':

* When an new subscription is created
* When a subscription is changed
* When a subscription is cancelled
* When a payment for a subscription succeeds
* When a recurring subscription payment fails

In the webhook alert URL field, point this to the `hook.php` file on your server. For example: `http://www.example.com/src/hook.php`


### Subscribing New Users

When subscribing new users, aside from sending the user to the checkout page, there is only one extra step you need to take. Use the `passthrough` checkout parameter to pass the ID of the user through to the checkout.

This will be used to identify and associate subscriptions with users when the webhook alerts come through.

Information on checkout links, and how to pass your users ID via the passthrough parameter can be found [here](https://www.paddle.com/docs/checkout)


### User Subscription Status

Subscriptions can have a few different statuses in your `subscriptions` database table.

### trialing
The user is in their initial trial period.
### active
The user’s subscription is active and their payments are up-to-date.
### past_due
The subscription is still active, but we're having trouble billing the user. You decide how you want to handle this situation; either continuing to give the user access, or waiting until their subscription moves back into the active state.
### cancelled
The subscription has been cancelled.

An example query to confirm a user is active on our `subscriptions` table would look like:

```sql
SELECT * FROM subscriptions WHERE user_id = '123' AND status = 'active';
```
