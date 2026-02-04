import React from 'react'
import { type SharedData } from '@/types'
import { Head, Link, usePage } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { NavigationMenu, NavigationMenuList, NavigationMenuItem, NavigationMenuLink } from '@/components/ui/navigation-menu'
import { LanguageSwitcher } from '@/components/language-switcher'

export default function TermsOfUse() {
  const { auth } = usePage<SharedData>().props;

  return (
    <>
      <Head title="Fakturo.app - Terms of Use">
        <meta name="robots" content="noindex" />
        <meta property="og:title" content="Fakturo.app - Terms of Use" />
        <meta property="og:url" content="https://www.fakturo.app/terms-of-use" />
        <meta property="og:site_name" content="https://www.fakturo.app" />
        <link rel="canonical" href="https://www.fakturo.app/terms-of-use" />
        <meta name="apple-itunes-app" content="app-id=6475118566" />
        <meta name="google-play-app" content="app-id=app.fakturo" />
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
      </Head>

      <div className="flex min-h-screen flex-col bg-gradient-to-b from-background to-muted text-foreground">
        {/* Header */}
        <header className="border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
          <div className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
            <div className="flex items-center gap-2">
              <img src="/assets/images/logo_transparent.png" alt="Fakturo Logo" className="h-8 w-auto" />
              <span className="text-xl font-bold">Fakturo.app</span>
            </div>

            <NavigationMenu>
              <NavigationMenuList>
                <NavigationMenuItem>
                  <NavigationMenuLink
                    className="group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    href="/"
                  >
                    Home
                  </NavigationMenuLink>
                </NavigationMenuItem>
                {auth.user && (
                  <NavigationMenuItem>
                    <NavigationMenuLink
                      className="group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                      href={route('dashboard')}
                    >
                      Dashboard
                    </NavigationMenuLink>
                  </NavigationMenuItem>
                )}
              </NavigationMenuList>
            </NavigationMenu>

            <div className="flex items-center gap-2">
              <LanguageSwitcher />

              {!auth.user ? (
                <>
                  <Button variant="ghost" asChild>
                    <Link href={route('login')}>Log in</Link>
                  </Button>
                  <Button asChild>
                    <Link href={route('register')}>Register</Link>
                  </Button>
                </>
              ) : (
                <Button variant="outline" asChild>
                  <Link href={route('dashboard')}>Dashboard</Link>
                </Button>
              )}
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 md:py-16">
          <Card className="overflow-hidden">
            <CardContent className="p-8">
              <div className="flex flex-wrap gap-4 mb-8">
                <Button variant="outline" asChild>
                  <Link href="/privacy-policy" className="text-foreground">
                    Data & Privacy Policy
                  </Link>
                </Button>
                <Button variant="default" asChild>
                  <Link href="/terms-of-use" className="text-primary-foreground">
                    Terms of Use
                  </Link>
                </Button>
              </div>

              <div className="prose prose-slate dark:prose-invert max-w-none">
                <p className="mb-4">
                  Contact and Account deletion: <a href="mailto:contact@fakturo.app" className="text-green-700 hover:no-underline"><b>c</b><b>o</b><b>n</b><b>t</b><b>a</b><b>c</b><b>t</b>@fakturo.app</a>
                </p>
                <p className="mb-4">
                  Last updated: January 19, 2024
                </p>

                <h1 className="text-4xl leading-[60px] font-bold mb-4">Terms of use</h1>
                <p className="mb-4">
                  PLEASE READ THESE TERMS OF USE (THE "TOU") CAREFULLY AS THEY CONTAIN IMPORTANT INFORMATION REGARDING YOUR LEGAL RIGHTS, REMEDIES, AND OBLIGATIONS.
                </p>

                <h2 className="text-3xl leading-12 font-bold mb-4">Definitions</h2>
                <ul className="mb-4 pl-6">
                  <li className="mb-4">
                    The 'Service' means all services run and made available by Us from time to time, namely the website fakturo.app, web application fakturo.app, the mobile application for
                    iOS
                    and Android and all other related services.
                  </li>
                  <li className="mb-4">
                    'Fakturo' or 'We' or 'Us' is the provider and operator of the Service
                  </li>
                  <li className="mb-4">
                    'User' or 'You' means any person which signs up to Fakturo und completes the registration process or decides to use Service without registration for browsing
                    etc..
                  </li>
                  <li className="mb-4">
                    'Parties' means User and Us together or each individually as 'Party'.
                  </li>
                  <li className="mb-4">
                    'Agreement' means a contractual relationship between Parties which results from accepting this TOU.
                  </li>
                  <li className="mb-4">
                    'Applicable Law' is any law, statute, regulation or subordinate legislation in force to which a Party is subject and/or in any jurisdiction that the Service is provided to
                    or
                    in respect of.
                  </li>
                </ul>

                <h2 className="text-3xl leading-12 font-bold mb-4">1. Introductory provisions</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">
                    By signing up for the Services You accept the TOU as outlined below.
                    These TOU constitute a binding legal agreement between You and Us
                    and governs any access to and use of the Service.
                  </li>
                  <li className="mb-4">
                    Please also carefully read our Privacy Policy and Data Protection Agreement. Privacy Policy and Data Protection Agreement constitute an integral part of these TOU and by accepting these TOU you also accept the Privacy Policy and Data Protection Agreement.
                  </li>
                  <li className="mb-4">
                    You agree that You have read and understood the TOU upon acceptance when You make successful completion of the registration process.
                  </li>
                  <li className="mb-4">
                    Our Service is directed to entrepreneurs and businesses.
                  </li>
                  <li className="mb-4">
                    By confirming these TOU You confirm and guarantee that, according to all valid legal provisions of England and Wales and the country of Your citizenship or residence, that You are authorized to conclude a valid Agreement with Us which is established by the confirmation of these TOU. If You confirm these TOU for a company or another legal entity, You represent and warrant that You have the authority to bind that company or other legal entity to TOU and, in such event, “You”, “Your”, “User” will refer and apply to that company or other legal entity
                  </li>
                  <li className="mb-4">
                    User must be over 16 years of age to access and use the Service. We may at Our sole discretion use any technical or other measures to prevent any person that is not authorized from accessing or using the Service.
                  </li>
                  <li className="mb-4">
                    When using the Service You may allow third parties to use some parts of Service as well (e.g. by adding other users to your account or enabling access to your bookkeeper/accountant). In case You allow any third parties to use the Service as well You are responsible to legally ensure that such third parties will be governed by these TOU when using the Service. The breach of these TOU by such third party shall be considered a breach of these TOU by You.
                  </li>
                  <li className="mb-4">
                    We may change these TOU anytime without prior notice and You agree that such changes will be binding to You. Any changes posted will amend and form part of this TOU. You are responsible for reviewing the TOU on a regular basis to obtain timely notice of any changes. The TOU are valid and in force from the moment of publication in the Service and by using the Service You express Your consent with new TOU.
                  </li>
                  <li className="mb-4">
                    If You don’t agree with terms presented in these TOU then You are not authorized to use the Service and You should cease use of the Service. In such a case your access to the Service may be limited.
                  </li>
                  <li className="mb-4">
                    The provisions and regulations of this TOU apply accordingly to any other software, program or application created by Us to use and access the Service at any time in the future for mobile devices, tablets or any other devices.
                  </li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">2. Our Service</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">Fakturo allows its Users to create invoices, record expenses, track payments and inventory, create statistics, communicate with other Users and use other features that We may decide to add to the Service, such as calendar feature or Marketplace for selling goods and services.</li>
                  <li className="mb-4">Features included in Service may also be specified on our website, web application or mobile application. By adding a specification of feature to the website, web application or mobile application or making a feature available for Users a feature shall become a part of Service.</li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">3. Inbox</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">Inbox is a feature of the Service, that allows Users to communicate with each other. Users shall only be able to contact Users added into their contact database. Inbox feature can be enabled or disabled by the decision of the Service by any time in the future.</li>
                  <li className="mb-4">To use the Inbox, You need a User account.</li>
                  <li className="mb-4">To contact another User you must either know their account ID or their e-mail. If a person does not have a User account by contacting the person You shall send it an invitation to the Service.</li>
                  <li className="mb-4">If You allow Our Service access to Your phone contacts database, We may be able to import e-mail addresses of Your contacts into the Service.</li>
                  <li className="mb-4">You agree to not use Inbox feature to:
                    a) spread any information that is illegal, obscene, defamatory, threatening, intimidating, harassing, hateful, racially or ethnically offensive, or instigate or encourage conduct that would be illegal or otherwise inappropriate, including promoting violent crimes;
                    b) publishing falsehoods, misrepresentations, or misleading statements;
                    c) impersonate someone;
                    d) involve sending illegal or impermissible communications, such as bulk messaging, auto-messaging, auto-dialing.</li>
                  <li className="mb-4">We do not control or guarantee the quality of interaction between Users, the User is solely responsible for any interaction with other Users or third parties. We do not verify the identity of other Users or third parties, if You have any doubts about the identity of a User or third party You should act responsibly and verify their identity by Yourself.</li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">4. Invoice payment</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">Invoice payment is a feature of the Service, that allows Users to send invoices and other documents through the Inbox. Invoice payment can be enabled or disabled by the decision of the Service by any time in the future.</li>
                  <li className="mb-4">By sending an invoice to a person without a User account, the person shall receive the invoice to his/her e-mail address along with an invitation to create a User account to either communicate with the User directly or to use a dedicated feature.</li>
                  <li className="mb-4">Invoice received via the Service may be paid using a payment method We provide in the Service. This may include PayPal, Credit Card payment or other methods. Payment method shall be governed by Terms of use of provider of the Payment provider or Payment gate (PayPal, Stripe, Credit card company, etc.).
                  </li>
                  <li className="mb-4">In case We decide to provide an own payment option, individual Terms of use governing the payment method shall apply.
                  </li>
                  <li className="mb-4">Please note, that We are not responsible for accuracy, validity or regularity of invoices You send or receive through the Service. Consider contacting the sender of the invoice to confirm the validity of documents received.
                  </li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">5. Calendar and appointments</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">Calendar and appointments are a feature of the Service, that contains a calendar to help Users with their time management. By sharing the calendar with other Users, it is possible to set appointments with Users in contact database or send invites to various events (meetings etc.). Calendar and appointments feature can be enabled or disabled by the decision of the Service by any time in the future.
                  </li>
                  <li className="mb-4">Service may allow its Users to connect their calendar, including third party calendars and time organizers (i.e. Google calendar, Apple calendar, etc.) to the Service.
                  </li>
                  <li className="mb-4">By connecting a calendar to the Service, You shall be able to send invitations to other Users and third parties and inform them about the time schedule.
                  </li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">6. Marketplace</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4">Marketplace is a feature of our Service, that allows Users to buy and sell goods and services using the Service as a platform. Marketplace can be enabled or disabled by the decision of the Service by any time in the future.</li>
                  <li className="mb-4">
                    <div className="mb-1">Following terms for Marketplace shall apply:
                    </div>
                    <div className="mb-1">The Service only serves as a platform that provides its Users with a tool to buy and sell goods and services from each other or with third parties. The Service and the Marketplace feature serves as a platform for a trade. We are not responsible for any kind of deals or contracts between Users and/or third parties. The User is solely responsible for any kind of contract, agreement or commitment between other Users and/or third parties, and for obligations from such contract, agreement or commitment.
                    </div>
                    <div className="mb-1">In case We should be, according to applicable laws and regulations, responsible for quality or quantity of any goods or services offered through the Service, Our responsibility shall be limited to the minimal requirements of the law and shall not exceed the amount You have paid for the use of the Service.
                    </div>
                    <div className="mb-1">Marketplace is free of charge, however an additional fee may be charged to Users who sell goods or offer services. The amount of applicable fee for using Marketplace shall be published on the website or in the app and you shall be notified beforehand. This fee is non-refundable, and its amount may be tied to amount of orders You received (or confirm) using the Service regardless of whether the transaction was concluded successfully or if the order was canceled for any reason.
                    </div>
                    <div className="mb-1">By accepting this TOU and selling goods or services through service You agree that payments for ordered goods and services may also pass through secure bank account provided by Our bank service provider, if we decide to enable such payment option. After the order is processed, an additional fee for using the Marketplace may be charged on the payment, the rest of the payment will be forwarded to Your bank account. You agree that We may withhold the payment made by Your customer up to 60 days after the order was place to ensure the delivery of ordered goods and services and safeguard customer rights. We shall use this right to withhold the payment if under applicable laws we may be liable for your delivery of ordered goods or services or breach of customer rights.
                    </div>
                    <div className="mb-1">We do not allow You to offer any kind of weapons (i.e knifes, guns, rifles), drugs, medication, living animals or items that violate any regulations of country in which they are offered in the Service. We also prohibit offering of any services that may violate regulations of the country in which they are offered in the Service. Any breach of this provision shall result in termination of Your User account and we may notify the relevant authorities to resolve any breach of law that may have occurred.
                    </div>
                    <div className="mb-1">As a User offering goods or services in the Service You shall be responsible and You warrant that You:
                        <div>have all necessary permits and licenses for selling goods or services offer through the Service to other Users and third parties and all goods and services offered through the Service comply with regulations of country in which the goods and services are offered;
                        </div>
                        <div>have published in the Service your own terms and conditions of sale, that are in accordance with regulation of country in which You offer your goods and services (We may provide You with a blank template of terms and conditions, however the template cannot be considered as an adequate replacement for Your own terms and conditions and should be considered only as an illustration. Fakturo is not liable for any inconsistencies or conflicts between the provided template and laws and regulations applicable for sell of good and services in Users country or Country in which goods and services are sold.);
                        </div>
                        <div>shall uphold all rights of your customers according to laws and regulation applicable to a trade made using the Service (i.e. provide customer with necessary minimal warranty, replace faulty goods, deliver goods and services in timely manner and in agreed quality, etc.);
                        </div>
                        <div>shall not breach any rights of third parties by offering your goods or services through the Service;
                        </div>
                        <div>shall provide Us with accurate and valid contact information to publish this information in the Service and provide them to any third parties shall request Your contact details with regards to any claims against You;
                        </div>
                        <div>defend and indemnify Us and Our affiliates and subsidiaries, and their officers, directors, employees and agents against all losses, costs (including reasonable legal costs and accounting fees on a full indemnity basis), expenses, demands or liability that they incur arising out of, or in connection with, a third party claim against Us relating to Your access or Your use of the Service and its Marketplace feature, mainly any breaches of these TOU and applicable laws and regulations resulting from Your sales through Services.
                        </div>
                    </div>
                    <div className="mb-1">As a User buying and browsing goods and services of other Users You agree to:
                      <div>always read terms and conditions of Users offering You his goods and services, since terms of buying goods and services (i.e. delivery time, refunds, etc.) are governed exclusively by terms and conditions of the Users offering goods and services You browse;
                      </div>
                      <div>enforce Your claims directly against the User that sold You or offered You his goods and services.
                      </div>
                    </div>
                    <div className="mb-1">We do not provide delivery services for any goods or services ordered through the Service unless We specifically state otherwise. Individual terms and conditions may apply in case We shall provide delivery services for delivery of goods or services ordered through the Service.
                    </div>
                    <div className="mb-1">Payments for goods and services ordered through the Service may be accomplish with payment methods we made available in the Service. This may include PayPal, Credit Card payment or other methods. Payment method shall be governed by Terms of use of the payment provider (PayPal, Stripe, Credit card company, etc.). An additional fee for using such payment methods and services may be charged. In case We decide to provide Our own option for payment, individual Terms of use governing the payment method shall apply.
                    </div>
                    <div className="mb-1">You acknowledge that In case We provide You with a template of terms and conditions for Your sale of goods and services through the Service, this template shall not constitute a legal advice and we shall be in no way liable for compliance of this template with applicable laws and regulations.
                    </div>
                    <div className="mb-1">We reserve a right to monitor all transactions made through the Service and deny processing of any transaction or end any use of Service at our sole discretion, if We have a suspicion that the use of service by a User is fraudulent or breaches these TOU or laws and regulations.
                    </div>
                  </li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">7. General Terms of Use</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">8. Creating and managing an account</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">9. Changes and modifications of the Service</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">10. Fees and payment conditions</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">11. Responsibility and warranty</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">12. Data protection and collection</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">13. Termination of the Agreement</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">14. Final provisions</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <hr/>

                <h2 className="text-3xl leading-12 font-bold mb-4">Data processing agreement</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">Definitions</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">2. Introduction</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">3. Data processing provisions</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">4. Data Processor Responsibilities</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">5. Controller Responsibilities</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">6. Details of processing</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">7. Subprocessors</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">8. Data storage and security</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">9. Confidentiality</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">10. Notification obligations</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                <h2 className="text-3xl leading-12 font-bold mb-4">11. Data retrieval</h2>
                <ol className="mb-4 pl-6">
                  <li className="mb-4"></li>
                </ol>

                {/* Placeholder for additional content - will be added incrementally */}
                <div className="border-t border-gray-200 mt-8 pt-4">
                  <p className="text-sm text-gray-600">
                    This terms of use document is effective as of the date stated above and may be updated from time to time.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </main>

        {/* Footer */}
        <footer className="border-t border-border/40 bg-muted/50 mt-auto">
          <div className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div className="flex flex-col md:flex-row justify-between items-center gap-4">
              <div className="flex items-center gap-2">
                <img
                  src="/assets/images/logo_with_text_on_right.png"
                  alt="Fakturo Logo"
                  className="h-10 w-auto"
                />
              </div>
              <div className="flex flex-wrap justify-center md:justify-end gap-6">
                <Link
                  href="/privacy-policy"
                  className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                  Privacy Policy
                </Link>
                <Link
                  href="/terms-of-use"
                  className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                  Terms of Service
                </Link>
                <span className="text-sm text-muted-foreground">DUNS - 496139257</span>
              </div>
            </div>
            <div className="mt-8 text-center text-sm text-muted-foreground">
              <p>© {new Date().getFullYear()} Fakturo.app. All rights reserved.</p>
            </div>
          </div>
        </footer>
      </div>
    </>
  )
}
