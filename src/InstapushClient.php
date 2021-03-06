<?php
/**
 * This file is part of the fnayou/instapush-php project.
 *
 * Copyright (c) 2017. Aymen FNAYOU <fnayou.aymen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fnayou\InstapushPHP;

use Fnayou\InstapushPHP\Http\HttpClientConfigurator;
use Fnayou\InstapushPHP\Http\HttpClientConfiguratorInterface;
use Fnayou\InstapushPHP\Transformer\ModelTransformer;
use Fnayou\InstapushPHP\Transformer\TransformerInterface;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;

/**
 * Class InstapushClient.
 */
final class InstapushClient
{
    /** @var \Http\Client\HttpClient */
    private $httpClient;

    /** @var \Http\Message\RequestFactory */
    private $requestFactory;

    /** @var \Fnayou\InstapushPHP\Transformer\TransformerInterface */
    private $transformer;

    /** @var bool */
    private $handleException = true;

    /**
     * @param \Http\Client\HttpClient                               $httpClient
     * @param \Http\Message\RequestFactory                          $requestFactory
     * @param \Fnayou\InstapushPHP\Transformer\TransformerInterface $transformer
     */
    public function __construct(
        HttpClient $httpClient,
        RequestFactory $requestFactory = null,
        TransformerInterface $transformer = null
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->transformer = $transformer ?: new ModelTransformer();
    }

    /**
     * @param \Fnayou\InstapushPHP\Http\HttpClientConfiguratorInterface $httpClientConfigurator
     * @param \Http\Message\RequestFactory                              $requestFactory
     * @param \Fnayou\InstapushPHP\Transformer\TransformerInterface     $transformer
     *
     * @return $this
     */
    public static function configure(
        HttpClientConfiguratorInterface $httpClientConfigurator,
        RequestFactory $requestFactory = null,
        TransformerInterface $transformer = null
    ) {
        $httpClient = $httpClientConfigurator->createConfiguredClient();

        return new static($httpClient, $requestFactory, $transformer);
    }

    /**
     * @param string $userToken
     * @param string $appIdentifier
     * @param string $appSecret
     *
     * @return $this
     */
    public static function create(string $userToken = null, string $appIdentifier = null, string $appSecret = null)
    {
        $httpClientConfigurator = new HttpClientConfigurator();
        $httpClientConfigurator
            ->setApiUserToken($userToken)
            ->setApiAppIdentifier($appIdentifier)
            ->setApiAppSecret($appSecret);

        return static::configure($httpClientConfigurator);
    }

    /**
     * @param string $userToken
     *
     * @return $this
     */
    public static function createForUser(string $userToken)
    {
        $httpClientConfigurator = new HttpClientConfigurator();
        $httpClientConfigurator
            ->setApiUserToken($userToken);

        return static::configure($httpClientConfigurator);
    }

    /**
     * @param string $appIdentifier
     * @param string $appSecret
     *
     * @return $this
     */
    public static function createForApp(string $appIdentifier, string $appSecret)
    {
        $httpClientConfigurator = new HttpClientConfigurator();
        $httpClientConfigurator
            ->setApiAppIdentifier($appIdentifier)
            ->setApiAppSecret($appSecret);

        return static::configure($httpClientConfigurator);
    }

    /**
     * @return \Http\Client\HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param \Http\Client\HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Http\Message\RequestFactory
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * @param \Http\Message\RequestFactory $requestFactory
     */
    public function setRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return \Fnayou\InstapushPHP\Transformer\TransformerInterface
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param \Fnayou\InstapushPHP\Transformer\TransformerInterface $transformer
     */
    public function setTransformer(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @return bool
     */
    public function isHandleException()
    {
        return $this->handleException;
    }

    /**
     * @param bool $handleException
     */
    public function setHandleException(bool $handleException)
    {
        $this->handleException = $handleException;
    }

    /**
     * @return Api\ApplicationsApi
     */
    public function applications()
    {
        return new Api\ApplicationsApi($this);
    }

    /**
     * @return Api\EventsApi
     */
    public function events()
    {
        return new Api\EventsApi($this);
    }

    /**
     * @return Api\NotificationApi
     */
    public function notification()
    {
        return new Api\NotificationApi($this);
    }
}
