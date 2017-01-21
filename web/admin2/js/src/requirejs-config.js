require.config({
  paths: {
    jquery: 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min',

    // Common
    px:             'pixeladmin',
    'px-libs':      'libs',
    'px-bootstrap': 'bootstrap',

    // Dependencies
    Chartist:         'libs/chartist',
    c3:               'libs/c3',
    d3:               'libs/d3',
    'datatables.net': 'libs/jquery.dataTables',
    moment:           'libs/moment',
  },
  priority: ['jquery'],
});
