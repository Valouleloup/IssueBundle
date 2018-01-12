Valouleloup Issue Bundle
========================

This bundle provides an issue forum system on your symfony application.

Install
--------------

Add ` valouleloup/issue-bundle ` to your ` composer.json ` file:

` composer require valouleloup/issue-bundle `

Register the bundle in app/AppKernel.php:

```php
public function registerBundles()
    {
        $bundles = [
            ...
            new Valouleloup\IssueBundle\ValouleloupIssueBundle(),
            ...
        ];
    }
```

Configuration
--------------

Configure your ` routing.yml ` file:

```yml
valouleloup_issue:
    resource: "@ValouleloupIssueBundle/Resources/config/routing.yml"
    prefix:   /issue-bundle
```

Add this configuration in your ` config.yml ` file and replace ` AppBundle\Entity\MyUser ` by your entity user :

```yml
doctrine:
    ...

    orm:
        ...
        resolve_target_entities:
            Symfony\Component\Security\Core\User\UserInterface: AppBundle\Entity\MyUser
```

Usage
--------------

You can now go to the url and enjoy the forum bundle :

` myapplication.com/issue-bundle/themes `

` myapplication.com/issue-bundle/issues/list `

` myapplication.com/issue-bundle/tags `

Examples
--------------

You can find a demo project that use this bundle :

  * [**TheIssue Project**][1]


Enjoy!

[1]:  https://github.com/valentin-biig/forum
