#Moneris API Changelog

###0.6.3 (2017-01-09)

####Changed

- Adds code style fixer. Updates code style. Updates md files [`1eda9ca3a6`](https://github.com/craigpaul/moneris-api/commit/1eda9ca3a6) 

###0.6.2 (2017-01-09)

####Changed

- Updates card_verification validation to not require amount [`714b1624ce`](https://github.com/craigpaul/moneris-api/commit/714b1624ce) 

###0.6.1 (2016-12-28)

####Changed

- Merge pull request #4 from tappleby/bugfix-payment-success [`cb3b75ed84`](https://github.com/craigpaul/moneris-api/commit/cb3b75ed84) 
- Updates transaction to reset errors array indices [`262617c3ac`](https://github.com/craigpaul/moneris-api/commit/262617c3ac) 
- Update successful payment logic, AVS + CVD can fail but the transaction will still be successful. [`bbd85b8063`](https://github.com/craigpaul/moneris-api/commit/bbd85b8063) 
- Merge remote-tracking branch 'upstream/master' [`0f4f6597a6`](https://github.com/craigpaul/moneris-api/commit/0f4f6597a6)

####Added

- Adds another caveat to docs [`14525afdce`](https://github.com/craigpaul/moneris-api/commit/14525afdce) 
- Adds caveat example to readme [`7c35112183`](https://github.com/craigpaul/moneris-api/commit/7c35112183) 

###0.6.0 (2016-11-27)

####Changed

- Updates readme [`735686c4d7`](https://github.com/craigpaul/moneris-api/commit/735686c4d7)
 
####Added

- Adds item attached to a purchase test [`b95b9fb382`](https://github.com/craigpaul/moneris-api/commit/b95b9fb382) 
- Adds ability to attach items to a purchase [`9fdb5ca317`](https://github.com/craigpaul/moneris-api/commit/9fdb5ca317)

###0.5.2 (2016-10-26)

####Changed

- Renames mocks to stubs [`78014491e2`](https://github.com/craigpaul/moneris-api/commit/78014491e2)
- Renames Stubs to mocks [`f6ce06e709`](https://github.com/craigpaul/moneris-api/commit/f6ce06e709)
- Updates expiring test to have a mocked response to overcome Moneris Api limits [`d3a813df34`](https://github.com/craigpaul/moneris-api/commit/d3a813df34)
- Removes old gitkeep files [`4d5a293a26`](https://github.com/craigpaul/moneris-api/commit/4d5a293a26)
- Autoloads stubs in development [`3db6228eda`](https://github.com/craigpaul/moneris-api/commit/3db6228eda)

####Added
   
- Adds mock handler helper function [`ac85c82adf`](https://github.com/craigpaul/moneris-api/commit/ac85c82adf) 
- Adds logic to get all ResolveData keys when more then one exists in the response [`c2393778da`](https://github.com/craigpaul/moneris-api/commit/c2393778da) 
- Adds docblock entry for transaction [`efed307cd7`](https://github.com/craigpaul/moneris-api/commit/efed307cd7)   
- Adds an expiring card stub [`d6c3cabb06`](https://github.com/craigpaul/moneris-api/commit/d6c3cabb06) 

###0.5.1 (2016-10-25)

####Changed
 
- Response:: => self:: [`61192d0b5e`](https://github.com/craigpaul/moneris-api/commit/61192d0b5e)  
- Fix test + sprintf formatting [`a88c97b7dc`](https://github.com/craigpaul/moneris-api/commit/a88c97b7dc)  
- Fix typo with expdate. [`aaf3f8dfd7`](https://github.com/craigpaul/moneris-api/commit/aaf3f8dfd7) 

####Added

- Add some extra handling for invalid cc / exp date where moneris doesnt return a status code. [`63bcbad7ef`](https://github.com/craigpaul/moneris-api/commit/63bcbad7ef)
- Add unit test for expdate conversion [`4c99e6f1ed`](https://github.com/craigpaul/moneris-api/commit/4c99e6f1ed)

###0.5.0 (2016-10-25)

####Changed

- Changes amount cast to string [`57b0a212e0`](https://github.com/craigpaul/moneris-api/commit/57b0a212e0)
- Updates customer tests to match new preparable setting [`6fa73e5961`](https://github.com/craigpaul/moneris-api/commit/6fa73e5961)
- Adjusts customer to use preparable instead of direct setting [`85ba6e9f66`](https://github.com/craigpaul/moneris-api/commit/85ba6e9f66)
- Extracts prepare method to preparable trait [`66deaddfd6`](https://github.com/craigpaul/moneris-api/commit/66deaddfd6)
- Extracts param appender and accounts for recursion [`57f71419e2`](https://github.com/craigpaul/moneris-api/commit/57f71419e2)

####Added

- Adds proper setter for customer data [`9da220ec66`](https://github.com/craigpaul/moneris-api/commit/9da220ec66) 
- Adds purchase with customer info tests [`fbff9e7f0d`](https://github.com/craigpaul/moneris-api/commit/fbff9e7f0d) 
- Adds vault customer info pre authorization test [`01846f8956`](https://github.com/craigpaul/moneris-api/commit/01846f8956) 
- Adds pre authorization test that includes customer info [`ca72ea6630`](https://github.com/craigpaul/moneris-api/commit/ca72ea6630)  
- Adds faker for tests [`ccea71a15d`](https://github.com/craigpaul/moneris-api/commit/ccea71a15d)   
- Adds extra normalization steps to prepare [`942b8d0b49`](https://github.com/craigpaul/moneris-api/commit/942b8d0b49)  
- Adds customer attaching tests to vault credit card methods [`eb01acfc2c`](https://github.com/craigpaul/moneris-api/commit/eb01acfc2c) 
- Adds missing customer information to add and update methods [`db07e976c8`](https://github.com/craigpaul/moneris-api/commit/db07e976c8)  
- Adds string check to account for SimpleXMLElements being passed through [`43f3c92070`](https://github.com/craigpaul/moneris-api/commit/43f3c92070)

###0.4.1 (2016-10-25)

####Changed

- Updates validation to provide title, field and code in errors for localization purposes [`e9888b9633`](https://github.com/craigpaul/moneris-api/commit/e9888b9633) 

###0.4.0 (2016-10-25)

####Added

- Adds 5.6 php to versions [`78200b2581`](https://github.com/craigpaul/moneris-api/commit/78200b2581) 

####Changed

- Removes type hints and updates docblocks [`f7950f5cbb`](https://github.com/craigpaul/moneris-api/commit/f7950f5cbb) 
- Lowers php requirements to 5.6 [`466ad201af`](https://github.com/craigpaul/moneris-api/commit/466ad201af) 
- Removes all php7 references for 5.6 support [`a69e6a1ef7`](https://github.com/craigpaul/moneris-api/commit/a69e6a1ef7) 

###0.3.0 (2016-10-25)

####Added

- Adds avs and cvd result params [`df1adb4f11`](https://github.com/craigpaul/moneris-api/commit/df1adb4f11)
- Adds receipt class as a wrapper for the Moneris response [`313e6f70ac`](https://github.com/craigpaul/moneris-api/commit/313e6f70ac)
- Adds guzzle dependency to constructors [`1c5ca81562`](https://github.com/craigpaul/moneris-api/commit/1c5ca81562)
- Adds guzzle dependency to constructor [`376c4a7909`](https://github.com/craigpaul/moneris-api/commit/376c4a7909)
- Adds test listener for Mockery [`707e299c75`](https://github.com/craigpaul/moneris-api/commit/707e299c75)
- Adds guzzlehttp/guzzle [`e73f910111`](https://github.com/craigpaul/moneris-api/commit/e73f910111)

####Changed

- Applies php-cs-fixer fixes [`f0c7a81744`](https://github.com/craigpaul/moneris-api/commit/f0c7a81744) 
- Updates receipt reading method for tests [`42ce4b4666`](https://github.com/craigpaul/moneris-api/commit/42ce4b4666)
- Updates attribute check test [`2286c2ce24`](https://github.com/craigpaul/moneris-api/commit/2286c2ce24)
- Changes receipt reading methods [`f2958c7701`](https://github.com/craigpaul/moneris-api/commit/f2958c7701)
- Changes raw curl to guzzle and accepts guzzle through the constructor [`4985650eae`](https://github.com/craigpaul/moneris-api/commit/4985650eae)
- Removes static references from tests [`02d23c1b5d`](https://github.com/craigpaul/moneris-api/commit/02d23c1b5d)
- Changes Processor from static to an instantiatable class [`ebf3791954`](https://github.com/craigpaul/moneris-api/commit/ebf3791954)
- Applies php-cs-fixer fixes [`6f056d64c0`](https://github.com/craigpaul/moneris-api/commit/6f056d64c0)
- Fixes docblocks [`32158db5e4`](https://github.com/craigpaul/moneris-api/commit/32158db5e4)
- Fixes spelling mistake in description [`0bcdb73fb4`](https://github.com/craigpaul/moneris-api/commit/0bcdb73fb4)

###0.2.1 (2016-10-23)

####Added

- Adds avs and cvd card verification [`e69410bc50`](https://github.com/craigpaul/moneris-api/commit/e69410bc50)
- Adds avs and cvd preauth tests [`ba9a90ff64`](https://github.com/craigpaul/moneris-api/commit/ba9a90ff64)
- Adds avs purchase test [`a417518da8`](https://github.com/craigpaul/moneris-api/commit/a417518da8)
- Adds cvd purchase test [`54b995da55`](https://github.com/craigpaul/moneris-api/commit/54b995da55)
- Adds test to capture a vault pre authorized credit card [`dc13140983`](https://github.com/craigpaul/moneris-api/commit/dc13140983)
- Adds avs vault pre auth test [`c1d4b1cf30`](https://github.com/craigpaul/moneris-api/commit/c1d4b1cf30)
- Adds vault pre auth cvd test [`38a2185e34`](https://github.com/craigpaul/moneris-api/commit/38a2185e34)
- Adds vault preauth to efraud array [`96aa2fdae7`](https://github.com/craigpaul/moneris-api/commit/96aa2fdae7)
- Adds vault pre authorization test [`4295d1e67a`](https://github.com/craigpaul/moneris-api/commit/4295d1e67a)
- Adds vault pre authorization functionality [`faa56b3913`](https://github.com/craigpaul/moneris-api/commit/faa56b3913)
- Adds vault avs purchase test [`d6c0e3e206`](https://github.com/craigpaul/moneris-api/commit/d6c0e3e206)
- Adds missing validator rules for cvd and avs [`9cb0c6c0b4`](https://github.com/craigpaul/moneris-api/commit/9cb0c6c0b4)
- Adds vault cvd purchase test [`f7d7cba87b`](https://github.com/craigpaul/moneris-api/commit/f7d7cba87b)
- Adds vault cvd purchase functionality [`0fc4104d17`](https://github.com/craigpaul/moneris-api/commit/0fc4104d17)
- Adds vault purchase test [`35c0a40ad4`](https://github.com/craigpaul/moneris-api/commit/35c0a40ad4)
- Adds purchase ability through vault [`8f186ef189`](https://github.com/craigpaul/moneris-api/commit/8f186ef189)

###0.2.0 (2016-10-21)

####Changed

- Removes useless import [`719ae5c414`](https://github.com/craigpaul/moneris-api/commit/719ae5c414)
- Removes handle method and adds receipt method [`a8c9f4237a`](https://github.com/craigpaul/moneris-api/commit/a8c9f4237a)
- Changes static moneris constructor to return Gateway [`61145f4bb0`](https://github.com/craigpaul/moneris-api/commit/61145f4bb0)

####Added

- Adds test for receipt method [`4329c15dea`](https://github.com/craigpaul/moneris-api/commit/4329c15dea)  
- Adds get expiring test [`4178cb8dcd`](https://github.com/craigpaul/moneris-api/commit/4178cb8dcd) 
- Adds get expiring functionality [`3e6358b3aa`](https://github.com/craigpaul/moneris-api/commit/3e6358b3aa) 
- Adds full lookup test [`4a7947b94a`](https://github.com/craigpaul/moneris-api/commit/4a7947b94a) 
- Adds full lookup functionality [`47fe25265b`](https://github.com/craigpaul/moneris-api/commit/47fe25265b) 
- Adds masked lookup test [`bee49511b4`](https://github.com/craigpaul/moneris-api/commit/bee49511b4) 
- Adds masked lookup functionality [`80025a8403`](https://github.com/craigpaul/moneris-api/commit/80025a8403) 
- Adds tokenization test [`4378b33c5d`](https://github.com/craigpaul/moneris-api/commit/4378b33c5d) 
- Adds tokenization functionality [`97b5fc1621`](https://github.com/craigpaul/moneris-api/commit/97b5fc1621) 
- Adds test helpers and var-dumper for testing purposes [`ed0c92baf8`](https://github.com/craigpaul/moneris-api/commit/ed0c92baf8) 
- Adds delete credit card test [`4a930e790c`](https://github.com/craigpaul/moneris-api/commit/4a930e790c) 
- Adds delete credit card functionality [`be50122c87`](https://github.com/craigpaul/moneris-api/commit/be50122c87) 
- Adds update credit card test [`eb342d11e1`](https://github.com/craigpaul/moneris-api/commit/eb342d11e1) 
- Adds update credit card functionality [`c38a72b4f0`](https://github.com/craigpaul/moneris-api/commit/c38a72b4f0) 
- Adds case for updating credit cards [`445ed72a1a`](https://github.com/craigpaul/moneris-api/commit/445ed72a1a) 
- Adds settable trait [`cb42fa92ec`](https://github.com/craigpaul/moneris-api/commit/cb42fa92ec) 
- Adds set up and add method test [`7401861c64`](https://github.com/craigpaul/moneris-api/commit/7401861c64) 
- Adds add card method. Extends default gateway [`d1522e2c71`](https://github.com/craigpaul/moneris-api/commit/d1522e2c71) 
- Adds transaction validator for res_add_cc [`e4192498c4`](https://github.com/craigpaul/moneris-api/commit/e4192498c4)  
- Adds instantiation tests [`9d3a6706a7`](https://github.com/craigpaul/moneris-api/commit/9d3a6706a7) 
- Adds static create constructor [`93bc912a78`](https://github.com/craigpaul/moneris-api/commit/93bc912a78) 
- Adds docblock to cards method [`7f18cc0b01`](https://github.com/craigpaul/moneris-api/commit/7f18cc0b01) 
- Adds customer attaching test [`cc6a054364`](https://github.com/craigpaul/moneris-api/commit/cc6a054364) 
- Adds customer attaching functionality [`32cb5d094c`](https://github.com/craigpaul/moneris-api/commit/32cb5d094c) 
- Adds settable trait [`c9536e7057`](https://github.com/craigpaul/moneris-api/commit/c9536e7057) 
- Adds test for customer instantiation [`51b4aaabda`](https://github.com/craigpaul/moneris-api/commit/51b4aaabda) 
- Adds customer object [`03ea0dca79`](https://github.com/craigpaul/moneris-api/commit/03ea0dca79) 
- Adds instantiation tests for CreditCard [`8b6fedd5f9`](https://github.com/craigpaul/moneris-api/commit/8b6fedd5f9) 
- Adds credit card object [`9b4536697a`](https://github.com/craigpaul/moneris-api/commit/9b4536697a)  

###0.1.0 (2016-10-20)

####Changed

- Changes config value [`009e3079fb`](https://github.com/craigpaul/moneris-api/commit/009e3079fb)
- Removed useless property [`851681cbf6`](https://github.com/craigpaul/moneris-api/commit/851681cbf6)
- Removes deprecated --dev flag [`0e52950fdf`](https://github.com/craigpaul/moneris-api/commit/0e52950fdf)
- Removes unnecessary dependency. Updates travis.yml file [`b94ce770d1`](https://github.com/craigpaul/moneris-api/commit/b94ce770d1)
- Removes script from travel.yml [`11b01f43bd`](https://github.com/craigpaul/moneris-api/commit/11b01f43bd)
- Changes order id to be unique-ish. Adds toXml test [`2d60a32d23`](https://github.com/craigpaul/moneris-api/commit/2d60a32d23)
- Changes order id to be unique-ish [`e062edca91`](https://github.com/craigpaul/moneris-api/commit/e062edca91)
- Finishes off basic purchase flow [`8a547233b6`](https://github.com/craigpaul/moneris-api/commit/8a547233b6)
- Updates mistake in purchase to rewrite existing params variable with merged array [`613cb0de36`](https://github.com/craigpaul/moneris-api/commit/613cb0de36)
- Updates processor to handle invalid transactions. Adds skeleton for response class [`dc72200996`](https://github.com/craigpaul/moneris-api/commit/dc72200996)
- Updates property docblocks [`199e6e6926`](https://github.com/craigpaul/moneris-api/commit/199e6e6926)
- Removes external validator and poorly thought through internal validation classes [`f683f6f9b1`](https://github.com/craigpaul/moneris-api/commit/f683f6f9b1)
- Reformats array to single line [`67f21b967e`](https://github.com/craigpaul/moneris-api/commit/67f21b967e)
- Moves id, token and environment to TestCase [`a933892857`](https://github.com/craigpaul/moneris-api/commit/a933892857)
- Extends TestCase [`af058a8683`](https://github.com/craigpaul/moneris-api/commit/af058a8683)
- Updates static to $this in docblock [`0bb52932d8`](https://github.com/craigpaul/moneris-api/commit/0bb52932d8)
- Updates autoloader to load base test case [`bb85703b9b`](https://github.com/craigpaul/moneris-api/commit/bb85703b9b)
- Changes gateway method name [`d61432029c`](https://github.com/craigpaul/moneris-api/commit/d61432029c)
- Normalizes docblock descriptions [`2285ee3b67`](https://github.com/craigpaul/moneris-api/commit/2285ee3b67)
- Moves magic getter to trait [`4469fbe44b`](https://github.com/craigpaul/moneris-api/commit/4469fbe44b)
- Updates formatting [`0cce230570`](https://github.com/craigpaul/moneris-api/commit/0cce230570)

####Added

- Adds capture transaction test [`afabd68496`](https://github.com/craigpaul/moneris-api/commit/afabd68496)
- Adds capture transaction functionality [`c35dc53577`](https://github.com/craigpaul/moneris-api/commit/c35dc53577)
- Adds missing docblock description [`306f094875`](https://github.com/craigpaul/moneris-api/commit/306f094875)
- Adds refund transaction test [`911f7c5f65`](https://github.com/craigpaul/moneris-api/commit/911f7c5f65)
- Adds refund transaction functionality [`4dceb1c511`](https://github.com/craigpaul/moneris-api/commit/4dceb1c511)
- Adds void transaction test [`c6d524f829`](https://github.com/craigpaul/moneris-api/commit/c6d524f829)
- Adds void transaction functionality [`7e2ebb0fb3`](https://github.com/craigpaul/moneris-api/commit/7e2ebb0fb3)
- Adds test for card verification [`6eb5dc30e2`](https://github.com/craigpaul/moneris-api/commit/6eb5dc30e2)
- Adds card verification functionality [`1a5eec173f`](https://github.com/craigpaul/moneris-api/commit/1a5eec173f)
- Adds preauth test [`eeb2df97a8`](https://github.com/craigpaul/moneris-api/commit/eeb2df97a8)
- Adds support for preauth [`fa0a1c8921`](https://github.com/craigpaul/moneris-api/commit/fa0a1c8921)
- Adds code coverage package [`ceff55f925`](https://github.com/craigpaul/moneris-api/commit/ceff55f925)
- Adds avs and cvd tests [`f9f92565b0`](https://github.com/craigpaul/moneris-api/commit/f9f92565b0)
- Adds errors property [`b905798960`](https://github.com/craigpaul/moneris-api/commit/b905798960)
- Adds settable trait [`c27570d632`](https://github.com/craigpaul/moneris-api/commit/c27570d632)
- Adds filter for code coverage reporting [`1575a26c07`](https://github.com/craigpaul/moneris-api/commit/1575a26c07)
- Adds CVD and AVS support [`6a5b54b547`](https://github.com/craigpaul/moneris-api/commit/6a5b54b547)
- Adds ability to use credit_card as a key to replace pan [`0fff82995b`](https://github.com/craigpaul/moneris-api/commit/0fff82995b)
- Adds build status button from travis ci [`241d984a92`](https://github.com/craigpaul/moneris-api/commit/241d984a92)
- Adds travis.yml file [`74e010ab9c`](https://github.com/craigpaul/moneris-api/commit/74e010ab9c)
- Adds response tests [`8047a03cab`](https://github.com/craigpaul/moneris-api/commit/8047a03cab)
- Adds proper transaction for successful processor test [`a14c21f2da`](https://github.com/craigpaul/moneris-api/commit/a14c21f2da)
- Adds processor test [`36ce8585e4`](https://github.com/craigpaul/moneris-api/commit/36ce8585e4)
- Adds settable functionality through __set [`88cf5c0d20`](https://github.com/craigpaul/moneris-api/commit/88cf5c0d20)
- Adds tests for valid and invalid transactions [`8dbc17c459`](https://github.com/craigpaul/moneris-api/commit/8dbc17c459)
- Adds valid and invalid checks. Adds static validator methods for extraction [`0eb47a9e83`](https://github.com/craigpaul/moneris-api/commit/0eb47a9e83)
- Adds transaction tests [`59706d0b05`](https://github.com/craigpaul/moneris-api/commit/59706d0b05)
- Adds prepare functionality [`b868a88c2a`](https://github.com/craigpaul/moneris-api/commit/b868a88c2a)
- Adds purchase and validation tests. Extends TestCase [`ff055f3b52`](https://github.com/craigpaul/moneris-api/commit/ff055f3b52)
- Adds stubbed out purchase functionality and needed classes [`9a51ec56db`](https://github.com/craigpaul/moneris-api/commit/9a51ec56db)
- Adds validators and needed exception [`ba6d122450`](https://github.com/craigpaul/moneris-api/commit/ba6d122450)
- Adds crypt types for purchasing [`25dbe7620c`](https://github.com/craigpaul/moneris-api/commit/25dbe7620c)
- Adds gateway test [`11c0a35f92`](https://github.com/craigpaul/moneris-api/commit/11c0a35f92)
- Adds class docblock. Uses Gettable trait for property access [`9fb213236e`](https://github.com/craigpaul/moneris-api/commit/9fb213236e)
- Adds Moneris tests [`2ae5925690`](https://github.com/craigpaul/moneris-api/commit/2ae5925690)
- Adds initial Moneris and Gateway classes [`a31b63685f`](https://github.com/craigpaul/moneris-api/commit/a31b63685f)
- Adds .idea to ignore [`df3bb7e4b2`](https://github.com/craigpaul/moneris-api/commit/df3bb7e4b2)
- Adds blank tests folder [`63b179d409`](https://github.com/craigpaul/moneris-api/commit/63b179d409)
- Adds initial composer configuration [`8d42012c92`](https://github.com/craigpaul/moneris-api/commit/8d42012c92)
- Adds blank src folder [`3747a4640c`](https://github.com/craigpaul/moneris-api/commit/3747a4640c)
- Adds basic phpunit configuration [`21e315a9e3`](https://github.com/craigpaul/moneris-api/commit/21e315a9e3)
- Adds dot files [`3641e6ac50`](https://github.com/craigpaul/moneris-api/commit/3641e6ac50)
- Adds LICENSE [`3d65d289e3`](https://github.com/craigpaul/moneris-api/commit/3d65d289e3)
- Adds README [`27783d461a`](https://github.com/craigpaul/moneris-api/commit/27783d461a)