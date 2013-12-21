<?php

class OAuthAccounts extends SmartestSystemApplication{
    
    public function listClientAccounts(){
        
        $this->setTitle('OAuth Client Accounts');
        $this->setFormReturnUri();
        $this->setFormReturnDescription('OAuth accounts');
        $h = new SmartestOAuthHelper;
        $this->send($h->getAccounts(), 'accounts');
        
    }
    
    public function addAccount(){
        
        $this->setTitle('Add an OAuth-based service client');
        $services = SmartestOAuthHelper::getServices();
        $this->send($services, 'services');
        
    }
    
    public function insertAccount(){
        
        $account = new SmartestOAuthAccount;
        $account->setLabel($this->getRequestParameter('oauth_service_label'));
        $account->setOAuthServiceId($this->getRequestParameter('oauth_service'));
        $account->setRegisterDate(time());
        $account->setUsername('oauth:'.SmartestStringHelper::randomFromFormat('LLNNNNLL'));
        $account->setPassword('x');
        $account->save();
        
        $this->formForward();
        
    }
    
    public function editAccount(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
            $this->send($account, 'account');
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function updateAccount(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('oauth_account_id')) && $account->isOAuthClient()){
            
            $account->setLabel($this->getRequestParameter('oauth_service_label'));
            $account->setOAuthConsumerToken($this->getRequestParameter('oauth_consumer_token'));
            $account->setOAuthConsumerSecret($this->getRequestParameter('oauth_consumer_secret'));
            $account->setOAuthAccessToken($this->getRequestParameter('oauth_access_token'));
            $account->setOAuthAccessTokenSecret($this->getRequestParameter('oauth_access_token_secret'));
            $account->save();
            
            $this->addUserMessageToNextRequest("The account has been updated", SmartestUserMessage::SUCCESS);
            $this->formForward();
            
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function prepareAccessTokenRequestProcess(){
        
        $account = new SmartestOAuthAccount;
        
        if($account->find($this->getRequestParameter('account_id')) && $account->isOAuthClient()){
            $this->send($account, 'account');
        }else{
            $this->addUserMessageToNextRequest("The account ID was not recognised or the account is not an OAuth Client Account", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
    }
    
    public function receiveOAuthCallback(){
        
        
        
    }
    
    public function twitterSettings(){
        
        $this->setTitle('Site Twitter Account Settings');
        $this->send($this->getGlobalPreference('twitter_consumer_key'), 'twitter_consumer_key');
        $this->send($this->getGlobalPreference('twitter_consumer_secret'), 'twitter_consumer_secret');
        $this->send($this->getGlobalPreference('twitter_access_token'), 'twitter_access_token');
        $this->send($this->getGlobalPreference('twitter_access_token_secret'), 'twitter_access_token_secret');
        
        $token_request_callback_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->send($token_request_callback_url, 'callback_url');
        
        $token_request_signing_key = $this->getGlobalPreference('twitter_consumer_secret').'&';
        $this->send($token_request_signing_key, 'token_request_signing_key');
        
        $this->send(time(), 'timestamp');
        $this->send(SmartestStringHelper::random(32), 'nonce');
        
        $token_request_base_url = 'https://api.twitter.com/oauth/request_token?';
        
        $parameters = array();
        
        $parameters['oauth_consumer_key'] = $this->getGlobalPreference('twitter_consumer_key');
        $parameters['oauth_callback'] = $token_request_callback_url;
        $parameters['oauth_signature_method'] = 'HMAC-SHA1';
        $parameters['oauth_timestamp'] = time()-3600;
        $parameters['oauth_nonce'] = SmartestStringHelper::random(32);
        
        ksort($parameters);
        
        $base_string = 'GET&'.urlencode($token_request_base_url).'&'.urlencode(SmartestStringHelper::toUrlParameterString($parameters, false));
        $parameters['oauth_signature'] = base64_encode(SmartestStringHelper::toHmacSha1($base_string, $token_request_signing_key));
        ksort($parameters);
        
        $url = $token_request_base_url.SmartestStringHelper::toXmlEntities(SmartestStringHelper::toUrlParameterString($parameters));
        $this->send($url, 'token_request_url');
        
    }
    
    public function updateTwitterSettings(){
        
        $this->setGlobalPreference('twitter_consumer_key', $this->getRequestParameter('twitter_consumer_key'));
        $this->setGlobalPreference('twitter_consumer_secret', $this->getRequestParameter('twitter_consumer_secret'));
        $this->setGlobalPreference('twitter_access_token', $this->getRequestParameter('twitter_access_token'));
        $this->setGlobalPreference('twitter_access_token_secret', $this->getRequestParameter('twitter_access_token_secret'));
        $this->formForward();
        
    }
    
}