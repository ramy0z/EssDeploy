[![@coreui angular](https://img.shields.io/badge/@coreui%20-angular-lightgrey.svg?style=flat-square)](https://github.com/coreui/angular)
[![npm package][npm-coreui-angular-badge]][npm-coreui-angular] 
[![NPM downloads][npm-coreui-angular-download]][npm-coreui-angular]  
[![@coreui coreui](https://img.shields.io/badge/@coreui%20-coreui-lightgrey.svg?style=flat-square)](https://github.com/coreui/coreui)
[![npm package][npm-coreui-badge]][npm-coreui]
[![NPM downloads][npm-coreui-download]][npm-coreui]    
![angular](https://img.shields.io/badge/angular-^8.0.0-lightgrey.svg?style=flat-square)  

[npm-coreui-angular]: https://www.npmjs.com/package/@coreui/angular  
[npm-coreui-angular-badge]: https://img.shields.io/npm/v/@coreui/angular.png?style=flat-square  
[npm-coreui-angular-download]: https://img.shields.io/npm/dm/@coreui/angular.svg?style=flat-square  
[npm-coreui]: https://www.npmjs.com/package/@coreui/coreui
[npm-coreui-badge]: https://img.shields.io/npm/v/@coreui/coreui.png?style=flat-square
[npm-coreui-download]: https://img.shields.io/npm/dm/@coreui/coreui.svg?style=flat-square

# ESS Free Angular 2+ Admin Template [![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social&logo=twitter)](https://twitter.com/intent/tweet?text=ESS%20-%20Free%20Bootstrap%204%20Admin%20Template%20&url=https://coreui.io&hashtags=bootstrap,admin,template,dashboard,panel,free,angular,react,vue)

Please help us on [Product Hunt](https://www.producthunt.com/posts/coreui-open-source-bootstrap-4-admin-template-with-angular-2-react-js-vue-js-support) and [Designer News](https://www.designernews.co/stories/81127). Thanks in advance!

Curious why I decided to create ESS? Please read this article: [Jack of all trades, master of none. Why Bootstrap Admin Templates suck.](https://medium.com/@lukaszholeczek/jack-of-all-trades-master-of-none-5ea53ef8a1f#.7eqx1bcd8)

ESS is an Open Source Bootstrap Admin Template. But ESS is not just another Admin Template. It goes way beyond hitherto admin templates thanks to transparent code and file structure. And if that's not enough, let’s just add that CoreUI consists bunch of unique features and over 1000 high quality icons.

ESS is based on Bootstrap 4 and offers 6 versions: 
[HTML5 AJAX](https://github.com/coreui/coreui-free-bootstrap-admin-template-ajax), 
[HTML5](https://github.com/coreui/coreui-free-angular-admin-template), 
[Angular 2+](https://github.com/coreui/coreui-free-angular-admin-template), 
[React.js](https://github.com/coreui/coreui-free-react-admin-template), 
[Vue.js](https://github.com/coreui/coreui-free-vue-admin-template)
 & [.NET Core 2](https://github.com/mrholek/ESS-NET).

ESS is meant to be the UX game changer. Pure & transparent code is devoid of redundant components, so the app is light enough to offer ultimate user experience. This means mobile devices also, where the navigation is just as easy and intuitive as on a desktop or laptop. The ESS Layout API lets you customize your project for almost any device – be it Mobile, Web or WebApp – ESS covers them all!

## Table of Contents

* [Versions](#versions)
* [ESS Pro](#coreui-pro)
* [Admin Templates built on top of ESS Pro](#admin-templates-built-on-top-of-coreui-pro)
* [Installation](#installation)
* [Usage](#usage)
* [What's included](#whats-included)
* [Documentation](#documentation)
* [Contributing](#contributing)
* [Versioning](#versioning)
* [Creators](#creators)
* [Community](#community)
* [Community Projects](#community-projects)
* [License](#license)
* [Support ESS Development](#support-coreui-development)

## Versions

ESS is built on top of Bootstrap 4 and supports popular frameworks.

* [ESS Free Bootstrap Admin Template](https://github.com/coreui/coreui-free-bootstrap-admin-template)
* [ESS Free Bootstrap Admin Template (Ajax)](https://github.com/coreui/coreui-free-bootstrap-admin-template-ajax)
* [ESS Free Angular 2+ Admin Template](https://github.com/coreui/coreui-free-angular-admin-template)
* 🚧 ESS Free .NET Core 2 Admin Template (Available Soon)
* [ESS Free React.js Admin Template](https://github.com/coreui/coreui-free-react-admin-template)
* [ESS Free Vue.js Admin Template](https://github.com/coreui/coreui-free-vue-admin-template)

## ESS Pro

* 💪  [ESS Pro Bootstrap Admin Template](https://coreui.io/pro/)
* 💪  [ESS Pro Bootstrap Admin Template (Ajax)](https://coreui.io/pro/)
* 💪  [ESS Pro Angular Admin Template](https://coreui.io/pro/angular)
* 💪  [ESS Pro React Admin Template](https://coreui.io/pro/react)
* 💪  [ESS Pro Vue Admin Template](https://coreui.io/pro/vue)

## Admin Templates built on top of ESS Pro

| ESS Pro | Prime | Root | Alba | Leaf |
| --- | --- | --- | --- | --- |
| [![ESS Pro Admin Template](https://coreui.io/assets/img/example-coureui.jpg)](https://coreui.io/pro/angular/)| [![Prime Admin Template](https://coreui.io/assets/img/responsive-prime.png)](https://coreui.io/admin-templates/angular/prime/?support=1)| [![Root Admin Template](https://coreui.io/assets/img/responsive-root.png)](https://coreui.io/admin-templates/angular/root/?support=1)| [![Alba Admin Template](https://coreui.io/assets/img/responsive-alba.png)](https://coreui.io/admin-templates/angular/alba/?support=1)| [![Leaf Admin Template](https://coreui.io/assets/img/responsive-leaf.png)](https://coreui.io/admin-templates/angular/leaf/?support=1)

#### Prerequisites
Before you begin, make sure your development environment includes `Node.js®` and an `npm` package manager.

###### Node.js
Angular requires `Node.js` version 8.x or 10.x

- To check your version, run `node -v` in a terminal/console window.
- To get `Node.js`, go to [nodejs.org](https://nodejs.org/).

###### Angular CLI
Install the Angular CLI globally using a terminal/console window.
```bash
npm install -g @angular/cli
```

##### Update to Angular 8
Angular 8 requires `Node.js` version 12.x   
Update guide - see: [https://update.angular.io](https://update.angular.io)

## Installation

### Clone repo

``` bash
# clone the repo
$ git clone https://github.com/coreui/coreui-free-angular-admin-template.git my-project

# go into app's directory
$ cd my-project

# install app's dependencies
$ npm install
```

## Usage

``` bash
# serve with hot reload at localhost:4200.
$ ng serve

# build for production with minification
$ ng build
```

## What's included

Within the download you'll find the following directories and files, logically grouping common assets and providing both compiled and minified variations. You'll see something like this:

```
free-angular-admin-template/
├── e2e/
├── src/
│   ├── app/
│   ├── assets/
│   ├── environments/
│   ├── scss/
│   ├── index.html
│   └── ...
├── .angular-cli.json
├── ...
├── package.json
└── ...
```

## Documentation

The documentation for the ESS Free Angularp Admin Template is hosted at our website [ESS](https://coreui.io/angular/)

## Contributing

Please read through our [contributing guidelines](https://github.com/coreui/coreui-free-angular-admin-template/blob/master/CONTRIBUTING.md). Included are directions for opening issues, coding standards, and notes on development.

Editor preferences are available in the [editor config](https://github.com/coreui/coreui-free-angular-admin-template/blob/master/.editorconfig) for easy use in common text editors. Read more and download plugins at <http://editorconfig.org>.

## Versioning

For transparency into our release cycle and in striving to maintain backward compatibility, ESS Free Admin Template is maintained under [the Semantic Versioning guidelines](http://semver.org/).

See [the Releases section of our project](https://github.com/coreui/coreui-free-angular-admin-template/releases) for changelogs for each release version.

## Creators

**Łukasz Holeczek**

* <https://twitter.com/lukaszholeczek>
* <https://github.com/mrholek>

**Andrzej Kopański**

* <https://github.com/xidedix>

## Community

Get updates on ESS's development and chat with the project maintainers and community members.

- Follow [@core_ui on Twitter](https://twitter.com/core_ui).
- Read and subscribe to [ESS Blog](https://coreui.ui/blog/).

### Community Projects

Some of projects created by community but not maintained by ESS team.

* [NuxtJS + Vue ESS](https://github.com/muhibbudins/nuxt-coreui)
* [Colmena](https://github.com/colmena/colmena)

## Copyright and license

copyright 2018 creativeLabs Łukasz Holeczek. Code released under [the MIT license](https://github.com/coreui/coreui-free-angular-admin-template/blob/master/LICENSE).
There is only one limitation you can't re-distribute the ESS as stock. You can’t do this if you modify the ESS. In past we faced some problems with persons who tried to sell ESS based templates.

## Support ESS Development

ESS is an MIT licensed open source project and completely free to use. However, the amount of effort needed to maintain and develop new features for the project is not sustainable without proper financial backing. You can support development by donating on [PayPal](https://www.paypal.me/holeczek), buying [ESS Pro Version](https://coreui.io/pro) or buying one of our [premium admin templates](https://genesisui.com/?support=1).

As of now I am exploring the possibility of working on ESS fulltime - if you are a business that is building core products using ESS, I am also open to conversations regarding custom sponsorship / consulting arrangements. Get in touch on [Twitter](https://twitter.com/lukaszholeczek).
