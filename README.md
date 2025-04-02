# DeliveryMatch Plugin Development Kit
The DeliveryMatch Plugin Development Kit (PDK) is built for creating easy integrations with PHP E-commerce platforms. This library is a wrapper around the [SDK](https://www.github.com/deliverymatch/SDK) 

## Requirements
* PHP >= 8.1
* A [PSR-17 implementation](https://packagist.org/providers/psr/http-factory-implementation)
* A [PSR-18 implementation](https://packagist.org/providers/psr/http-client-implementation)
* Composer

## Getting Started
Install the package using Composer:
```bash
composer require deliverymatch/pdk
```

### Implement the `Cache` interface
During the checkout process the PDK needs access the same data during multiple requests. Most E-commerce platforms give you access to cookie, session, cache and database storage.
```php
class MyWebshopDataCache implements Cache
{
    public function setShipmentId(int $shipmentId): void
    {
        Session::set($shipmentId, "dm_shipment_id");
    }

    public function getShipmentId(): ?int
    {
        return Session::get("dm_shipment_id");
    }

    public function setShippingOptions(array $shippingOptions): void
    {
        $shipmentId = $this->getShipmentId();

        Redis::save(key: $shipmentId, base64_encode(serialize($shippingOptions)));
    }

    public function getShippingOptions(): array
    {
        $shipmentId = $this->getShipmentId();

        if ($shipmentId === null) {
            return [];
        }

        $serializedOptions = Redis::get(key: $shipmentId);

        return unserialize(base64_decode($serializedOptions));
    }

    public function setCheckId(string $checkId): void
    {
        Session::set($checkId, "dm_check_id");
    }

    public function getCheckId(): ?string
    {
        return Session::get("dm_check_id");
    }

    public function flush(): void
    {
        $shipmentId = $this->getShipmentId();
        Redis::flush(key: $shipmentId);
        Session::clear("dm_check_id");
        Session::clear("dm_shipment_id");
    }
}

```


### Create a PDK Bootstrapper
The Bootstrapper is used to provide the dependency injection container in de PDK with an implementation of the `Cache` interface.
```php
class MyWebshopPdkBootstrapper extends \DeliveryMatch\Pdk\Common\PdkBootstrapper
{
    protected function getAdditionalConfiguration(): array
    {
        return [
            \DeliveryMatch\Pdk\Common\Cache::class => \DI\autowire(MyWebshopDataCache::class),
        ];
    }
}
```

### Interact with the PDK
Once the PDK is successfully bootstrapped you can use the PDK to interact with DeliveryMatch using the `\DeliveryMatch\Pdk\Facade\PDK`.

Check if the API connection is successful:
```php
$isAuthenticated = \DeliveryMatch\Pdk\Facade\Pdk::checkConnection()
```

Fetch shipping options:
```php
$rates = \DeliveryMatch\Pdk\Facade\Pdk::fetchShippingOptions($request)
```

Store the selected shipping option in the PDK:
```php
\DeliveryMatch\Pdk\Facade\Pdk::setSelectedOption($this->request->checkId);
```

Push the selected shipping option to DeliveryMatch. (Not efficient to do this after every method change in the checkout, best to do this after the checkout is completed)
```php
\DeliveryMatch\Pdk\Facade\Pdk::addShippingOptionToShipment()
```

Flush the cache when the checkout is completed. Store the shipment id with the corresponding order from the E-commerce platform.
```php
// add shipping option to DeliveryMatch
\DeliveryMatch\Pdk\Facade\Pdk::addShippingOptionToShipment();

// Get the cache to fetch the shipment id
$cache = \DeliveryMatch\Pdk\Facade\Pdk::get(MyWebshopDataCache::class);
$shipmentId = $cache->getShipmentId;

// Fetch E-Commerce order from the database
// Store dm shipmentId with the order
$eComOrder = Orders::get(123);
$eComOrder->dm_shipment_id = $shipmentId;
$eComOrder->save();

// Flush cache
$cache->flush();
```

Update the shipment to new when the payment is received. You also have the option to update the order number. Not all E-commerce platforms provide an oder number before the checkout is completed.  
```php
\DeliveryMatch\Pdk\Facade\Pdk::updateShipmentToNew(shipmentId: $shipmentId, orderNumber: $order->number)
```