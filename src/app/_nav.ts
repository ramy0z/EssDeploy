interface NavAttributes {
  [propName: string]: any;
}
interface NavWrapper {
  attributes: NavAttributes;
  element: string;
}
interface NavBadge {
  text: string;
  variant: string;
}
interface NavLabel {
  class?: string;
  variant: string;
}

export interface NavData {
  name?: string;
  url?: string;
  icon?: string;
  badge?: NavBadge;
  title?: boolean;
  children?: NavData[];
  variant?: string;
  attributes?: NavAttributes;
  divider?: boolean;
  class?: string;
  label?: NavLabel;
  wrapper?: NavWrapper;
}

export const navItemsAr: NavData[] = [
  {
    name: 'الرئيسية',url: '/dashboard',icon: 'icon-home'
    // ,badge: { variant: 'info',text: 'NEW' }
  },
  {title: true, name: 'الطلبات'},
    {name: 'طلب طباعة',url: '/orderprint',icon: 'icon-printer'},
    {name: 'طلباتى',url: '/myorder',icon: 'icon-basket'},
    {name: 'تقرير طلباتى',url: '/orderHistory',icon: 'icon-notebook'},
  {title: true,name: 'حساباتى'},
    {name: 'محفظتى',url: '/mywallet',icon: 'icon-wallet'},
    {name: 'حركات الحساب',url: '/myaccount',icon: 'icon-notebook'},
  {title: true,name: 'إعدادات الحساب'},
    {name: 'الصفحة الشخصية',url: '/profile',icon: 'icon-user'},
    {name: 'الحماية',url: '/security-info',icon: 'icon-lock'},
  {
    name: 'Pages',url: '/pages',icon: 'icon-star',
    children: [
      {name: 'تسجيل دخول',url: '/login',icon: 'icon-user'},
      {name: 'تسجيل جديد',url: '/register',icon: 'icon-star'},
      {name: 'Error 404',url: '/404',icon: 'icon-star'},
      {name: 'Error 500',url: '/500',icon: 'icon-star'}
    ]
  },
  {
    name: 'صفحات هامة', icon: 'icon-puzzle',
    children: [
      {name: 'من نحن',url: '/about-us'},{name: 'تواصل معنا',url: '/contact-us'},
      {name: 'الأسئلة الشائعة',url: '/faq',},{name: 'نقاط الاستلام',url: '/pickup-locations'},
      {name: 'سياسة الخصوصية',url: '/privacy-policy'},{name: 'الشروط والأحكام',url: '/terms-conditions'}
    ]
  }
];

export const navItemsEn: NavData[] = [
  {
    name: 'Home',url: '/dashboard',icon: 'icon-home'
    // ,badge: { variant: 'info',text: 'NEW' }
  },
  {title: true, name: 'Orders'},
    {name: 'Print Order',url: '/orderprint',icon: 'icon-printer'},
    {name: 'My Orders',url: '/myorder',icon: 'icon-basket'},
    {name: 'My Orders Reports',url: '/orderHistory',icon: 'icon-notebook'},
  {title: true,name: 'My Account'},
    {name: 'My Wallet',url: '/mywallet',icon: 'icon-wallet'},
    {name: 'Account Movments',url: '/myaccount',icon: 'icon-notebook'},
  {title: true,name: 'Account Settings'},
    {name: 'Profile',url: '/profile',icon: 'icon-user'},
    {name: 'Security',url: '/security-info',icon: 'icon-lock'},
  {
    name: 'Pages',url: '/pages',icon: 'icon-star',
    children: [
      {name: 'Login',url: '/login',icon: 'icon-user'},
      {name: 'Register',url: '/register',icon: 'icon-star'},
      {name: 'Error 404',url: '/404',icon: 'icon-star'},
      {name: 'Error 500',url: '/500',icon: 'icon-star'}
    ]
  },
  {
    name: 'Important Pages', icon: 'icon-puzzle',
    children: [
      {name: 'about-us',url: '/about-us'},{name: 'contact-us',url: '/contact-us'},
      {name: 'faq',url: '/faq',},{name: 'pickup-locations',url: '/pickup-locations'},
      {name: 'privacy-policy',url: '/privacy-policy'},{name: 'terms-conditions',url: '/terms-conditions'}
    ]
  }
];
