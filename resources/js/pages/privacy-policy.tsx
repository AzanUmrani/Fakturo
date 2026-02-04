import React from 'react'
import { type SharedData } from '@/types'
import { Head, Link, usePage } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { NavigationMenu, NavigationMenuList, NavigationMenuItem, NavigationMenuLink } from '@/components/ui/navigation-menu'
import { LanguageSwitcher } from '@/components/language-switcher'

export default function PrivacyPolicy() {
  const { auth } = usePage<SharedData>().props;

  return (
      <>
        <Head title="Fakturo.app - Data & Privacy Policy">
          <meta name="robots" content="noindex" />
          <meta property="og:title" content="Fakturo.app - Privacy Policy" />
          <meta property="og:url" content="https://www.fakturo.app/privacy-policy" />
          <meta property="og:site_name" content="https://www.fakturo.app" />
          <link rel="canonical" href="https://www.fakturo.app/privacy-policy" />
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
                  <Button variant="default" asChild>
                    <Link href="/privacy-policy" className="text-primary-foreground">
                      Data & Privacy Policy
                    </Link>
                  </Button>
                  <Button variant="outline" asChild>
                    <Link href="/terms-of-use" className="text-foreground">
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

                  <h3 className="text-2xl leading-9 font-bold mb-4">This document describes:</h3>
                  <p className="mb-4">
                    How Fakturo treats, stores, process and saves your
                    data including your personal data in the Service according to a Data
                    Protection Laws, specifically the General Data Protection Regulation
                    ("GDPR").
                  </p>

                  <h1 className="text-4xl leading-[60px] font-bold mb-4">Data &amp; Privacy Policy</h1>

                  <p className="mb-4">
                    This Data &amp; Privacy Policy (hereinafter as "Privacy Policy") are subject
                    to Terms of Use. Unless specified otherwise in this Privacy Policy the terms
                    used in this Privacy Policy shall have the same meaning as in the TOU.
                  </p>
                  <p className="mb-4">
                    Our Data &amp; Privacy Policy describe in detail how all information about
                    you is gathered and processed. As a User of our Service or a visitor to
                    Fakturo website, the security of your personal data is our primary focus. To
                    continue using our Service as a User you will need to accept our TOU, agree
                    to our Data Protection Agreement and agree with our Privacy Policy which
                    provide all details on how your data is gathered, processed, and protected.
                  </p>
                  <p className="mb-4">
                    "Fakturo" or 'we' is the provider and operator of the Service
                  </p>
                  <p className="mb-4">
                    'User' or 'you' means any person which signs up to Fakturo and completes the
                    registration process.
                  </p>
                  <p className="mb-4">
                    'Data Protection Law/s' means applicable and binding laws to which Fakturo
                    and User is a subject to in the field of personal data protection and
                    privacy especially GDPR.
                  </p>
                  <p className="mb-4">
                    'GDPR' means the Regulation (EU) 2016/679 of the European Parliament and of
                    the Council of 27 April 2016 on the protection of natural persons with
                    regard to the processing of personal data and on the free movement of such
                    data, and repealing Directive 95/46/EC (General Data Protection Regulation).
                  </p>
                  <p className="mb-4">
                    'Personal Data' has the meaning given to that term in Data Protection Laws.
                    It's any information relating to a data subject by which it can be
                    identified, directly or indirectly, in particular by reference to an
                    identifier such as a name, identification number, location data, online
                    identifier or to one or more factors specific to the physical,
                    physiological, genetic, mental, economic, cultural or social identity of
                    that natural person or legal entity (where applicable).
                  </p>
                  <p className="mb-4">
                    'Processing' has the meanings given to that term in Data Protection Laws
                    (and related terms such as 'process' have corresponding meanings).
                  </p>
                  <p className="mb-4">
                    This policy informs you which of your data and personal data is collected
                    and processed when you visit our website, use our web application or any
                    other services offered, how we use your data and personal data and what
                    rights you have regarding the use of your personal data. This privacy also
                    applies for the access and use of the mobile apps as well as the other
                    available services.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Introduction</h2>
                  <p className="mb-4">
                    Fakturo collects and processes some data which are necessary for a proper use
                    of the Service. Some of these data might be personal data which could
                    identify you as a live person and which are subject to Data Protection
                    Legislation and GDPR.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">What data we collect about you</h2>
                  <p className="mb-4">
                    We collect and process data:
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      in your account that you provide to us when you sign up for the
                      account and when you update these information from time to time such
                      as your name, surname, email address, telephone number, e-mail
                      address and others;
                    </li>
                    <li className="mb-4">
                      in your profile and billing account that you provided to us about
                      your profile and your business and company information, such as your
                      address, business ID number, phone number and others;
                    </li>
                    <li className="mb-4">
                      from your communications within the Service with us or with other
                      users such as your messages, comments and other form of
                      communication within the Service;
                    </li>
                    <li className="mb-4">
                      from your entries to fields and forms within the Service such as
                      search queries, forum posts, promotions, surveys and other features
                      of the Service.
                    </li>
                  </ol>

                  <p className="mb-4">
                    When you use the Service and its features we collect data about how you use
                    the Service such as:
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      data about your interactions with the Service and its features such
                      as your viewed pages, content, search queries, unique numbers of
                      applications and other interactions with the Service;
                    </li>
                    <li className="mb-4">
                      location data from the device that you use to access the Service and
                      we use various tools to determine your general location information
                      and also precise location information. Location information is
                      required to fullfill local Tax Authorities laws and Regulations and
                      process Invoicing data correctly.
                    </li>
                    <li className="mb-4">
                      protocols and log data about the method you use the Service such as
                      data from the device that you use to access the Service, your IP
                      address, device events (error reports, failures, system activities,
                      hardware settings), applications that you use to access the Service
                      (i.e. browser), browser language, Web storage site of a browser
                      (including HTML 5 technology) and buffering memory applications,
                      access time and referring URL address, hardware and software
                      information and other similar information;
                    </li>
                    <li className="mb-4">
                      data regarding all transactions made through the Service made
                      through third party payment systems;
                    </li>
                    <li className="mb-4">
                      data gathered from cookies and similar technologies (for more
                      information see our Cookies policy below).
                    </li>
                  </ol>

                  <p className="mb-4">
                    We also collect data about you from third parties and we may combine these
                    data with data we have about you such as
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      data from third parties services and webpages such as Facebook when
                      you choose to use it to connect to the Service (see details below);
                    </li>
                    <li className="mb-4">
                      data provided by users that you authorized to use the Service on
                      your behalf;
                    </li>
                    <li className="mb-4">
                      data from other sources that we may collect to the extent permitted
                      by applicable law.
                    </li>
                  </ol>

                  <h2 className="text-3xl leading-12 font-bold mb-4">What data we collect about you</h2>
                  <p className="mb-4">
                    As long as it is not necessary for the creation and maintenance of a
                    contractual relationship between you and Fakturo, we don't collect, gather
                    and process any personal data which could identify you as a person.
                  </p>
                  <p className="mb-4">
                    In order to ensure audit-proof processing of the data, the creation,
                    modification or deletion of data may be logged or it may be prevented
                    (especially according to the French anti-fraud law).
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">How do we process your data</h2>
                  <p className="mb-4">
                    Fakturo may, throughout your use of the Service, collect and process some of
                    your data. Fakturo will obtain and process these data through technical means
                    and processes in such a way that it will not be able under any circumstances
                    assign them to your User account or to you. Such data are thus fully
                    anonymous.
                  </p>
                  <p className="mb-4">
                    We generally use, process and store data including personal data that you
                    provided to us and that we collect to:
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      identify you as a contractual party or representative of a
                      contractual party to us;
                    </li>
                    <li className="mb-4">
                      enable you to access and use the Service in general or through your
                      User account;
                    </li>
                    <li className="mb-4">
                      enable you to communicate with us and enable our communication with
                      you such as sending you notifications, messages, reminders and any
                      other form of communication within the Service and otherwise (i.e.
                      email messages);
                    </li>
                    <li className="mb-4">
                      operate, protect, improve and optimize the Service, its features,
                      its user experience, make it more personal, to provide customer
                      support and to develop new services;
                    </li>
                    <li className="mb-4">
                      maintain a trusted and safe environment on the Service and to
                      prevent any actual or potential fraud, misconduct or other harmful
                      activity, investigation and risk assessment, enforcing all of our
                      Terms of Use and Privacy Policy and other similar actions which we
                      may do without notifying you;
                    </li>
                    <li className="mb-4">
                      send you marketing, advertising and promotional messages and
                      information that might be interesting to you about us and our
                      services. You may unsubscribe from these messages anytime;
                    </li>
                    <li className="mb-4">
                      to administer referral programs, rewards, surveys, sweepstakes,
                      contests, or other promotional activities or events sponsored or
                      managed by Fakturo or our third party business partners;
                    </li>
                    <li className="mb-4">
                      to comply with our legal obligations, resolve any disputes that we
                      may have with any of our users, and enforce our agreements with
                      third parties.
                    </li>
                  </ol>

                  <p className="mb-4">
                    We may also process, review, scan and/or analyse your communications with us
                    for fraud prevention, risk assessment, regulatory compliance, investigation,
                    product development, research and customer support purposes and other
                    similar purposes. You consent and agree that we may process, review, scan
                    and/or analyse your communications with us for these purposes.
                  </p>
                  <p className="mb-4">
                    We may also share or disclose some personal data to:
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      Third-party service providers: We may use service providers to
                      process data including your personal data on our behalf. This
                      processing is for several purposes, including for example sending
                      out marketing material. Third party service providers process
                      personal data only according to our instructions, under biding legal
                      agreement, are bound by confidentiality clauses and are not allowed
                      to use your personal data for other purposes.
                    </li>
                    <li className="mb-4">
                      Payment providers and (other) financial institutions: We may need to
                      share certain personal data with the payment service provider and
                      the relevant financial institution to handle payments from you and
                      to you. We may furthermore share data with relevant financial
                      institutions, if we consider it strictly necessary for fraud
                      detection and prevention purposes.
                    </li>
                    <li className="mb-4">
                      Competent authorities: We disclose personal data to law enforcement
                      insofar as it is required by law or is strictly necessary for the
                      prevention, detection or prosecution of criminal acts and fraud. We
                      may need to further disclose data to competent authorities to
                      protect and defend our rights or properties, or the rights and
                      properties of our business partners.
                    </li>
                  </ol>
                  <p className="mb-4">
                    Unless required by relevant Data Protection Laws Fakturo has no influence on
                    and assumes no liability for the compliance with Data Protection Laws
                    standards outside of our Service.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Legal basis for processing of your personal data</h2>
                  <p className="mb-4">
                    When we process personal data about you we are doing so on the following
                    legal basis:
                  </p>
                  <ol className="mb-4 pl-6">
                    <li className="mb-4">
                      Performance of a contract: The use of your personal data may be
                      necessary to perform the contract that you have with us as set out
                      in our Terms of Use.
                    </li>
                    <li className="mb-4">
                      Legitimate interests: We may use your personal data for our
                      legitimate interests, such as to enable you to access and use the
                      Service, provide you with the best suitable content in the Service,
                      to sent you informational, promotional and marketing emails and
                      newsletters, to operate, protect, improve and optimize the Service
                      and promote our products and services and the content on our
                      Service, and for administrative, fraud detection and legal purposes.
                    </li>
                    <li className="mb-4">
                      Performing of a legal obligation: We will process your personal data
                      when and for as long as required by applicable law.
                    </li>
                    <li className="mb-4">
                      Consent: Where there is no other legal basis for processing we may
                      ask for your consent to process your personal data for the purposes
                      described in these Privacy Policy for the duration of your User
                      account. You may at any time withdraw your consent to the processing
                      of your personal data by writing to us to the company address or to
                      an email address stated in this Privacy Policy.
                    </li>
                  </ol>
                  <p className="mb-4">
                    Fakturo does not process your personal data including profiling to make
                    automated decisions.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Maintaining of your personal data</h2>
                  <p className="mb-4">
                    Some data you provide to us in your User account may be personal data.
                    Personal data are provided by you freely and you are responsible to maintain
                    them accurate, true and complete. You may review, update, or delete the
                    personal data in your User account by logging into your User account and
                    reviewing your account settings and profile.
                  </p>
                  <p className="mb-4">
                    If you provide personal data of other persons to us (for example your
                    authorized personal data of users or your client data) you warrant and
                    guarantee that you are entitled to do so and that you have legal basis for
                    such action.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Transferring of your personal data</h2>
                  <p className="mb-4">
                    Fakturo stores and processes your personal data in the European Economic Area
                    ("EEA") using these companies:
                  </p>
                  <ul className="mb-4 pl-6">
                    <li className="mb-4">
                      WebCreators, s.r.o. - Slovakia
                    </li>
                  </ul>
                  <p className="mb-4">
                    Processing and storing personal data outside EEA is made in compliance with
                    applicable Data Protection Laws in these companies:
                  </p>
                  <ul className="mb-4 pl-6">
                    <li className="mb-4">
                      none
                    </li>
                  </ul>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Period of processing of your personal data</h2>
                  <p className="mb-4">
                    Fakturo stores and processes your personal data for the period necessary in
                    relation to the purpose of processing as described in this Privacy Policy
                    and local Legislation Authorities. We will process your personal data for as
                    long as you have an active User account and automaticaly delete them 10
                    years after your last sign-in. We may then anonymize your information for
                    statistical purposes.
                  </p>
                  <p className="mb-4">
                    We will terminate your personal data associated with your User account after
                    10 years of inactivity or when your Agreement with us has been terminated
                    and when you request a permanent deletion of your User account.
                  </p>
                  <p className="mb-4">
                    Deleted data will be removed from Fakturo servers its back-ups and all third
                    party companies listed in paragraph "<strong>Transferring of your personal
                    data</strong>" of Privacy Policy.
                  </p>
                  <p className="mb-4">
                    Even if you ask us to destroy your personal data Fakturo may be required to
                    process some of your personal data to comply with legal obligations, i.e. to
                    maintain accounting records and other obligations. We will process personal
                    data for this purpose for a period required by applicable laws.
                  </p>
                  <p className="mb-4">
                    Where we process your personal data based on your consent you may at any
                    time withdraw your consent to the processing of your personal data. We will
                    process personal data for this purpose until you withdraw your consent.
                  </p>
                  <p className="mb-4">
                    Where you are entitled to object to our processing of your personal data
                    (i.e. direct marketing) we will process personal data for this purpose until
                    you object to such processing (by unsubscribing from our emails).
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Signing up and logging via Google and Apple</h2>
                  <p className="mb-4">
                    Fakturo enables you to create an account and log in to your account via your
                    Google/Apple account. The authentification is made
                    for the purpose to make signup and login to your User account
                    easier. Any of the information collected by the Google/Apple may be used in
                    one of the following ways: to simplify the usage of Service or personalize
                    your user experience (your information helps us to better respond to your
                    needs)
                  </p>
                  <p className="mb-4">
                    Information that you provide are all visible in your
                    public profile (such as name and other public information). Fakturo also asks
                    you to share your e-mail through Google/Apple SignIn which you can deny or disable
                    later in the Fakturo settings.
                  </p>
                  <p className="mb-4">
                    Logging in to Service via your Google/Apple account is a possibility and not
                    mandatory. If you don't wish to connect via your Google/Apple account anymore,
                    contact our support team at <a href="mailto:contact@fakturo.app" className="text-green-700 hover:no-underline">contact@fakturo.app</a>.
                    As well, you can delete your account and all your data in your account settings.
                  </p>

                  <h2 className="text-3xl leading-12 font-bold mb-4">Data storage and security</h2>
                  <p className="mb-4">
                    Fakturo's servers are operated by WebCreators, s.r.o. with their Hosting service which
                    ensures fast and robust data protection on par with current data protection
                    legislative requirements. All the data you provide to the Fakturo app are
                    encrypted according to the security standard TLS (Transport Layer Security).
                    We use only secure connections via https://... .
                    All of your data, including their transmission
                    between your device and the Fakturo servers, will be protected by standard
                    security measures with the use of 256-bit SSL encryption.
                  </p>
                  <p className="mb-4">
                    We also take technical and organizational suitable security measures, in
                    order to protect your data against random or deliberate manipulations,
                    partial or complete losses, destruction and/or against unauthorized access.
                    In order to avoid loss of data, we run a mirrored database setup which means
                    that your data is always stored in two separate locations.
                  </p>
                  <p className="mb-4">
                    The personal data that we collect is stored in a secure environment within
                    the EEA in compliance with Privacy Shield rules and treated confidentially.
                    Access to this data is limited to selected Fakturo employees. We adhere
                    to Data Protection Laws at all times.
                  </p>
                  <p className="mb-4">
                    We do our best to secure your data in the best possible way, but we cannot
                    guarantee the safety of your data when transferred over the Internet. When
                    data is transferred over the Internet, there is a certain risk that others
                    can access the data illicitly.
                  </p>
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
