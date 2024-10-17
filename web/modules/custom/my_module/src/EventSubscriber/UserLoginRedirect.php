<?php

namespace Drupal\my_module\EventSubscriber;

use Drupal\core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Implements redirects for my_module Commerce.
 */
class UserLoginRedirect implements EventSubscriberInterface {

  /**
   * The current user object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a UserLoginRedirect object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current_user service object.
   */
  public function __construct(AccountProxyInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['kernel.request'][] = ['onRequest', 0];
    return $events;
  }

  /**
   * Handler for the kernel request event.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The RequestEvent object.
   */
  public function onRequest(RequestEvent $event) {
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = $event->getRequest();
    if ($request->attributes->get('_route') !== 'entity.user.canonical') {
      return;
    }
    $path = $request->getPathInfo();
    $paths = explode("/", $path);

    // Check if current user matches entity user (from order page for example)
    $order_user = $request->attributes->get('_entity');

    // Check if order user is of type user.
    if ($order_user->getEntityTypeId() == 'user') {
      // If so, grab the order user's uid.
      $order_uid = $order_user->id();
      if ($this->currentUser->id() != $order_uid) {
        return;
      }
    }

    // Redirect /user/* (except login/logout) to the my-account page.
    if (isset($paths[2]) && $paths[2] === 'user') {
      $moduleHandler = \Drupal::service('module_handler');
      if ($moduleHandler->moduleExists('language')) {
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      }
      // Check if it's active language module to redirect to /my-account page
      // depending language code.
      if ($language === $paths[1]) {
        $event->setResponse(new RedirectResponse('/' . $language . '/my-account'));
      }
      else {
        $event->setResponse(new RedirectResponse('/my-account'));
      }
    }
  }

}
