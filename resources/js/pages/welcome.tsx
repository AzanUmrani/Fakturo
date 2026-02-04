import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { NavigationMenu, NavigationMenuList, NavigationMenuItem, NavigationMenuLink } from '@/components/ui/navigation-menu';
import { ReceiptIcon, BarChart3Icon, ArrowRightIcon, CheckCircleIcon, SmartphoneIcon } from 'lucide-react';
import { LanguageSwitcher } from '@/components/language-switcher';
import AppearanceToggleDropdown from '@/components/appearance-dropdown';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    const storeLinks = {
        apple: 'https://apps.apple.com/sk/app/fakturo-custom-invoicing/id6475118566?platform=iphone',
        google: 'https://play.google.com/store/apps/details?id=app.fakturo&hl=sk'
    };

    return (
        <>
            <Head title="Welcome">
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
                                        href="#"
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
                            <AppearanceToggleDropdown />

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

                {/* Hero Section */}
                <section className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 md:py-24 lg:py-32">
                    <div className="grid gap-10 lg:grid-cols-2 lg:gap-16 items-center">
                        <div className="space-y-6">
                            <h1 className="text-4xl font-bold tracking-tighter sm:text-5xl md:text-6xl">
                                Generate invoices and estimates <span className="text-primary">in seconds</span>
                            </h1>
                            <p className="text-lg text-muted-foreground md:text-xl">
                                Effortlessly create professional invoices and estimates within seconds, streamlining your business processes with efficiency and precision.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4">
                                {!auth.user ? (
                                    <>
                                        <Button size="lg" asChild>
                                            <Link href={route('register')}>Get Started</Link>
                                        </Button>
                                        <Button size="lg" variant="outline" asChild>
                                            <Link href={route('login')}>Sign In</Link>
                                        </Button>
                                    </>
                                ) : (
                                    <Button size="lg" asChild>
                                        <Link href={route('dashboard')}>Go to Dashboard</Link>
                                    </Button>
                                )}
                            </div>

                            <div className="mt-6">
                                <p className="text-sm text-muted-foreground mb-3">Download our mobile app:</p>
                                <div className="flex flex-col sm:flex-row gap-3">
                                    <Button variant="outline" className="flex items-center gap-2" asChild>
                                        <a href={storeLinks.apple} target="_blank" rel="noopener noreferrer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 384 512" fill="currentColor">
                                                <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
                                            </svg>
                                            <span>App Store</span>
                                        </a>
                                    </Button>
                                    <Button variant="outline" className="flex items-center gap-2" asChild>
                                        <a href={storeLinks.google} target="_blank" rel="noopener noreferrer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512" fill="currentColor">
                                                <path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"/>
                                            </svg>
                                            <span>Google Play</span>
                                        </a>
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div className="relative mx-auto aspect-video overflow-hidden rounded-xl shadow-xl lg:order-last">
                            <img
                                src="/assets/images/phone_in_app_screen.png"
                                alt="Fakturo App on Phone"
                                className="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </section>

                {/* Features Section */}
                <section className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 md:py-24 lg:py-32 bg-muted/50">
                    <div className="mx-auto text-center mb-12 md:mb-16">
                        <h2 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl">Key Features</h2>
                        <p className="mt-4 text-muted-foreground md:text-xl">Everything you need to manage your invoices efficiently</p>
                    </div>
                    <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <Card className="overflow-hidden">
                            <CardHeader>
                                <CheckCircleIcon className="h-8 w-8 text-primary mb-2" />
                                <CardTitle>Easy to Use</CardTitle>
                                <CardDescription>Create professional invoices with just a few clicks</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <p>Our intuitive interface makes invoice generation simple and efficient, saving you time and effort.</p>
                                <div className="rounded-lg overflow-hidden">
                                    <img
                                        src="/assets/images/app_screens_1.png"
                                        alt="Easy to use interface"
                                        className="w-full h-auto object-cover"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                        <Card className="overflow-hidden">
                            <CardHeader>
                                <ReceiptIcon className="h-8 w-8 text-primary mb-2" />
                                <CardTitle>Professional Templates</CardTitle>
                                <CardDescription>Choose from a variety of professional invoice templates</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <p>Customize your invoices with our professionally designed templates to match your brand identity.</p>
                                <div className="rounded-lg overflow-hidden">
                                    <img
                                        src="/assets/images/app_screens_2.png"
                                        alt="Professional templates"
                                        className="w-full h-auto object-cover"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                        <Card className="overflow-hidden">
                            <CardHeader>
                                <BarChart3Icon className="h-8 w-8 text-primary mb-2" />
                                <CardTitle>Detailed Analytics</CardTitle>
                                <CardDescription>Track your business performance with detailed reports</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <p>Get insights into your business with comprehensive analytics and reporting features.</p>
                                <div className="rounded-lg overflow-hidden">
                                    <img
                                        src="/assets/images/app_screens_3.png"
                                        alt="Detailed analytics"
                                        className="w-full h-auto object-cover"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 md:py-24 lg:py-32">
                    <div className="mx-auto max-w-3xl text-center">
                        <h2 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl">Ready to streamline your invoicing?</h2>
                        <p className="mt-4 text-muted-foreground md:text-xl">Join thousands of businesses that trust Fakturo for their invoicing needs</p>
                        <div className="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                            {!auth.user ? (
                                <>
                                    <Button size="lg" asChild>
                                        <Link href={route('register')}>Get Started <ArrowRightIcon className="ml-2 h-4 w-4" /></Link>
                                    </Button>
                                </>
                            ) : (
                                <Button size="lg" asChild>
                                    <Link href={route('dashboard')}>Go to Dashboard <ArrowRightIcon className="ml-2 h-4 w-4" /></Link>
                                </Button>
                            )}
                        </div>

                        <div className="mt-8">
                            <p className="text-sm text-muted-foreground mb-4">Available on mobile devices:</p>
                            <div className="flex flex-col sm:flex-row justify-center gap-4">
                                <Button variant="outline" className="flex items-center gap-2" asChild>
                                    <a href={storeLinks.apple} target="_blank" rel="noopener noreferrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 384 512" fill="currentColor">
                                            <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
                                        </svg>
                                        <span>Download on App Store</span>
                                    </a>
                                </Button>
                                <Button variant="outline" className="flex items-center gap-2" asChild>
                                    <a href={storeLinks.google} target="_blank" rel="noopener noreferrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"/>
                                        </svg>
                                        <span>Get it on Google Play</span>
                                    </a>
                                </Button>
                            </div>
                        </div>
                        <div className="mt-12">
                            <img
                                src="/assets/images/app_screens_4.png"
                                alt="Fakturo App Screenshot"
                                className="w-full h-auto rounded-xl shadow-lg"
                            />
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t border-border/40 bg-muted/50">
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
    );
}
