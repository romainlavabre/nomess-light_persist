<?php


namespace Nomess\Component\LightPersist;

use Nomess\Component\Cache\CacheHandlerInterface;
use Nomess\Component\Cache\Exception\InvalidSendException;
use Nomess\Component\Config\Exception\ConfigurationNotFoundException;
use Nomess\Container\ContainerInterface;
use Nomess\Exception\NomessException;
use Nomess\Http\HttpRequest;
use Nomess\Http\HttpResponse;


class LightPersist implements LightPersistInterface
{
    private const CONFIGURATION_NAME = 'light_persist';
    private const COOKIE_NAME = 'psd_';
    private ContainerInterface    $container;
    private CacheHandlerInterface $cacheHandler;
    private ?array                $content = NULL;
    /**
     * Identifier of file
     */
    private ?string $id = NULL;
    
    
    /**
     * @Inject
     * @param ContainerInterface $container
     * @param CacheHandlerInterface $cacheHandler
     * @throws ConfigurationNotFoundException
     * @throws InvalidSendException
     * @throws NomessException
     */
    public function __construct(
        ContainerInterface $container,
        CacheHandlerInterface $cacheHandler
    )
    {
        $this->container    = $container;
        $this->cacheHandler = $cacheHandler;
        $this->getContent();
    }
    
    
    /**
     * @param $index
     * @return bool
     */
    public function has( $index ): bool
    {
        return isset( $this->content[$index] );
    }
    
    
    /**
     * Return value associate to index variable or null if doesn't exists
     *
     * @param mixed $index
     * @return mixed|void
     */
    public function &getReference( $index )
    {
        
        if( isset( $this->content[$index] ) ) {
            return $this->content[$index];
        }
    }
    
    
    /**
     * Add value in container
     *
     * @param mixed $key
     * @param mixed $value
     * @param bool $reset Delete value associate to index before instertion
     * @return void
     */
    public function set( $key, $value, $reset = FALSE ): void
    {
        if( $reset === TRUE ) {
            unset( $this->content[$key] );
        }
        
        if( \is_array( $value ) ) {
            
            foreach( $value as $keyArray => $valArray ) {
                
                $this->content[$key][$keyArray] = $valArray;
            }
        } else {
            $this->content[$key] = $value;
        }
    }
    
    
    /**
     * Return value associate to index variable or null if doesn't exists
     *
     * @param mixed $index
     * @return mixed
     */
    public function get( $index )
    {
        
        if( isset( $this->content[$index] ) ) {
            return $this->content[$index];
        } elseif( $index === '*' ) {
            return $this->content;
        } else {
            return NULL;
        }
    }
    
    
    /**
     * Delete an pair key/value
     *
     * @param string $index
     * @return void
     * @throws ConfigurationNotFoundException
     * @throws InvalidSendException
     * @throws NomessException
     */
    public function delete( string $index )
    {
        
        if( $this->id === NULL ) {
            $this->getContent();
        }
        
        if( array_key_exists( $index, $this->content ) ) {
            unset( $this->content[$index] );
        }
    }
    
    
    /**
     * Delete the persistence file
     */
    public function purge(): void
    {
        
        /**
         * @var HttpResponse
         */
        $response = $this->container->get( HttpResponse::class );
        
        $response->removeCookie( self::COOKIE_NAME );
        
        $this->cacheHandler->invalid(self::CONFIGURATION_NAME, $this->id);
    }
    
    
    /**
     * @throws InvalidSendException
     * @throws ConfigurationNotFoundException
     */
    private function getContent(): void
    {
        
        /**
         * @var HttpRequest
         */
        $request = $this->container->get( HttpRequest::class );
        
        $id = $request->getCookie( self::COOKIE_NAME );
        
        
        if( $id === NULL ) {
            
            /**
             * @var HttpResponse
             */
            $response = $this->container->get( HttpResponse::class );
            
            $id = uniqid();
            
            $response->addCookie( self::COOKIE_NAME, $id, time() + 60 * 60 * 24 * 3650, '/' );
            
        } else {
            $this->content = $this->cacheHandler->get(self::CONFIGURATION_NAME, $this->id);
        }
        
        $this->id = $id;
    }
    
    
    public function __destruct()
    {
        $this->cacheHandler->add(self::CONFIGURATION_NAME, [
            'value' => $this->content,
            'filename' => $this->id
        ]);
    }
}
