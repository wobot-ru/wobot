<?

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
//require_once('/var/www/daemon/fsearch3/ch.php');

$db = new database();
$db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

$links = array("http://instagram.com/p/rwV2Bslogc/","http://instagram.com/p/rvBTcRItzR/","http://instagram.com/p/ru9Ib-ot7i/","http://instagram.com/p/rttCahxjau/","http://instagram.com/p/rp0kV6Nz-0/","http://instagram.com/p/rpv1FVtz3o/","http://instagram.com/p/rphuT5lR7d/","http://instagram.com/p/rpF_y8retL/","http://instagram.com/p/rpEjihPDR9/","http://instagram.com/p/rmpXyJhS1h/","http://instagram.com/p/rkPRbQIe5Q/","http://instagram.com/p/rkMLEmo_LV/","http://instagram.com/p/rg_UWnPFwF/","http://instagram.com/p/rgOU6GnZm8/","http://instagram.com/p/rfc6hyG2zu/","http://instagram.com/p/repwY3Dktu/","http://instagram.com/p/repDciMf_2/","http://instagram.com/p/raX6Tup2VE/","http://instagram.com/p/rZrbmkElxV/","http://instagram.com/p/rZrbmkElxV/","http://instagram.com/p/rWfSh0BEIM/","http://instagram.com/p/rUrCXoJ8JD/","http://instagram.com/p/rSRCiBLCLS/","http://instagram.com/p/rSDFPizUyT/","http://instagram.com/p/rMt9ewnALR/","http://instagram.com/p/rMHaeVFBxi/","http://instagram.com/p/rKv25NE1oG/","http://instagram.com/p/rKmGpcE1mq/","http://instagram.com/p/rC_gGjGl8K/","http://instagram.com/p/rASjB7nm6Z/","http://instagram.com/p/rACULzxinF/","http://instagram.com/p/q8wje4mQub/","http://instagram.com/p/q66a7qCmNc/","http://instagram.com/p/q4jqyqhILV/","http://instagram.com/p/q4QJd1QI_u/","http://instagram.com/p/q19A_3KMCa/","http://instagram.com/p/qzw33BvJ_0/","http://instagram.com/p/qt6XcRy-vj/","http://instagram.com/p/qq9fLML0TY/","http://instagram.com/p/qptWydGn11/","http://instagram.com/p/qpchG-xAdD/","http://instagram.com/p/qpQr49NpVV/","http://instagram.com/p/qpQqaGNpVM/","http://instagram.com/p/qos2DuNpRT/","http://instagram.com/p/qoswTltpRK/","http://instagram.com/p/qnOTsLgSal/","http://instagram.com/p/qnGxuZgSeu/","http://instagram.com/p/qmopYcoyO8/","http://instagram.com/p/qmkO3RIyGn/","http://instagram.com/p/qmM1COPKoc/","http://instagram.com/p/qhfM__phgU/","http://instagram.com/p/qhaec9Pkue/","http://instagram.com/p/qhSYnlN2SG/","http://instagram.com/p/qhSQn5N2R4/","http://instagram.com/p/qelgVGESG8/","http://instagram.com/p/qelCPikSGV/","http://instagram.com/p/qefj6UJClk/","http://instagram.com/p/qcqMuXxmqh/","http://instagram.com/p/qcqMuXxmqh/","http://instagram.com/p/qZiqO0MqQb/","http://instagram.com/p/qXD4eWzDBF/","http://instagram.com/p/qXCXD_TDOE/","http://instagram.com/p/qW2DlvPYzX/","http://instagram.com/p/qUv0kTBh0U/","http://instagram.com/p/qSANGojzwt/","http://instagram.com/p/qR--VQjz-L/","http://instagram.com/p/qReCv8rhHn/","http://instagram.com/p/qPjTYvNOy6/","http://instagram.com/p/qMuOUgQfaC/","http://instagram.com/p/qMePqVBh2V/","http://instagram.com/p/qELEKVqLi5/","http://instagram.com/p/qEJcILKLhB/","http://instagram.com/p/qCjyTmPn_C/","http://instagram.com/p/qCfNV4S3jl/","http://instagram.com/p/qCMe9dJaFG/","http://instagram.com/p/p_37uEwKId/","http://instagram.com/p/p_smFxQKEk/","http://instagram.com/p/p9jaM6Rq_O/","http://instagram.com/p/p8rpIBOkyO/","http://instagram.com/p/p8rEh-BQPO/","http://instagram.com/p/p64UHbQKIE/","http://instagram.com/p/p62HveQKEx/","http://instagram.com/p/p6uwFqQKIz/","http://instagram.com/p/p6tmAOQKGz/","http://instagram.com/p/p4qTNpJdOh/","http://instagram.com/p/p1oeQQnySM/","http://instagram.com/p/p1S6qdg-mg/","http://instagram.com/p/p08TxZSFyr/","http://instagram.com/p/pwAXc3LMmy/","http://instagram.com/p/pve-rISBGj/","http://instagram.com/p/pth2liSHXN/","http://instagram.com/p/poyMbGwwhx/","http://instagram.com/p/pouX-Oh-q9/","http://instagram.com/p/popzj3K_Kw/","http://instagram.com/p/pjZ1w6NqRO/","http://instagram.com/p/pjRWF_wjtd/","http://instagram.com/p/pgnjMlSShS/","http://instagram.com/p/pgl7b3iapk/","http://instagram.com/p/pgfXOSSxsd/","http://instagram.com/p/pgHtlUtytm/","http://instagram.com/p/pe0yUkAdYP/","http://instagram.com/p/peRXWwNlxu/","http://instagram.com/p/peJ8FrCtmB/","http://instagram.com/p/peFAs1vKrm/","http://instagram.com/p/pdxG69M6z6/","http://instagram.com/p/pWuueVEn2V/","http://instagram.com/p/pWneEWik3-/","http://instagram.com/p/pRqLceBjWB/","http://instagram.com/p/pRhQn4FAFN/","http://instagram.com/p/pRNPC0gtxy/","http://instagram.com/p/pRDoOfNZah/","http://instagram.com/p/pQ0b-qDz7L/","http://instagram.com/p/pPSsEwvvRD/","http://instagram.com/p/pO_ubjnB7w/","http://instagram.com/p/pOtEVjFkfw/","http://instagram.com/p/oJuRWdpF8R/","http://instagram.com/p/pMxmIStGRs/","http://instagram.com/p/pMA6s8H_Ww/","http://instagram.com/p/pLf7b5nKpi/","http://instagram.com/p/pKGq48ltjT/","http://instagram.com/p/pJ2tBzHKjR/","http://instagram.com/p/pJ1xB7Na1B/","http://instagram.com/p/pH2e5qIt4g/","http://instagram.com/p/pHJGL3Jq1S/","http://instagram.com/p/pGrNavGCx3/","http://instagram.com/p/pGqkWDpecS/","http://instagram.com/p/pE2Adkhh4u/","http://instagram.com/p/pEsCiOzXVU/","http://instagram.com/p/pEHmGsMb2s/","http://instagram.com/p/pCHzW5I3LB/","http://instagram.com/p/pCEIITLlyb/","http://instagram.com/p/pBHjlRlnhp/","http://instagram.com/p/o_agIry90t/","http://instagram.com/p/o_IPxegLuy/","http://instagram.com/p/o-3FRBFtQu/","http://instagram.com/p/o-2vBLSfQ8/","http://instagram.com/p/o-0qMQEE2x/","http://instagram.com/p/o-xojZlUYg/","http://instagram.com/p/o-mUgTMNSX/","http://instagram.com/p/o9ZlHMu29p/","http://instagram.com/p/o9CcPISUYu/","http://instagram.com/p/o8-qaKHNkK/","http://instagram.com/p/o81pkVvB9I/","http://instagram.com/p/o8k5avMF2A/","http://instagram.com/p/o8BhjsKBRZ/","http://instagram.com/p/o6VQsPDdHa/","http://instagram.com/p/o6GcmbEOxg/","http://instagram.com/p/o6AgVbiUYQ/","http://instagram.com/p/o5-sMJiUVv/","http://instagram.com/p/o5orUYjCjo/","http://instagram.com/p/o3hV99i399/","http://instagram.com/p/o3ZwsEKzDs/","http://instagram.com/p/o3TnLWlNDs/","http://instagram.com/p/o0hcZAKLYr/","http://instagram.com/p/oyH34mGn_B/","http://instagram.com/p/ow1ahHGC0a/","http://instagram.com/p/owj6e6Brsc/","http://instagram.com/p/owZD51xWWX/","http://instagram.com/p/owVa_XxWQB/","http://instagram.com/p/ov0siOhrhz/","http://instagram.com/p/ovyuTQyx2_/","http://instagram.com/p/otxY1OOYNu/","http://instagram.com/p/otWgtYHv7x/","http://instagram.com/p/otAYeHHvzv/","http://instagram.com/p/os-zKOHvxE/","http://instagram.com/p/oshw4VyUd-/","http://instagram.com/p/orTD3wmn6n/","http://instagram.com/p/orPUV_uITU/","http://instagram.com/p/orOxZ2mFu7/","http://instagram.com/p/oq7NYyA9Hb/","http://instagram.com/p/oq27QgS3jW/","http://instagram.com/p/oqi_vTFkFs/","http://instagram.com/p/ooniTUwki_/","http://instagram.com/p/oomuMLQkhk/","http://instagram.com/p/ooU8mjNwpU/","http://instagram.com/p/ooPECUMpal/","http://instagram.com/p/ooNquKj-fN/","http://instagram.com/p/ooESRmL4Nu/","http://instagram.com/p/on2HGUsPzT/","http://instagram.com/p/ol-YplHtuo/","http://instagram.com/p/ojv-MRIRd4/","http://instagram.com/p/ojagk9mgxv/","http://instagram.com/p/ojS-YME_2o/","http://instagram.com/p/ojI0t9AFPn/","http://instagram.com/p/of-8XmIQYf/","http://instagram.com/p/of-6NNIQYb/","http://instagram.com/p/of-1qnoQYX/","http://instagram.com/p/of-rHDoQYK/","http://instagram.com/p/of-oR6oQYF/","http://instagram.com/p/of-mjQoQX_/","http://instagram.com/p/of-jCfoQX8/","http://instagram.com/p/ofwQetIQYC/","http://instagram.com/p/ofvadGoQXi/","http://instagram.com/p/ofvP72oQXZ/","http://instagram.com/p/oehYUYjJu7/","http://instagram.com/p/odUVkYIH9k/","http://instagram.com/p/odTIe6oH8Q/","http://instagram.com/p/obcMTrDz-Y/","http://instagram.com/p/obamGrMzpd/","http://instagram.com/p/oY2yJ9E8Xx/","http://instagram.com/p/oWYwhmqTWm/","http://instagram.com/p/oWRC6qJC4T/","http://instagram.com/p/oWDIOdMnqd/","http://instagram.com/p/oUMY6Uot7z/","http://instagram.com/p/oUC14SmkTm/","http://instagram.com/p/oT2ia9KBGm/","http://instagram.com/p/oT2ZJnqBGR/","http://instagram.com/p/oS5p71DsF6/","http://instagram.com/p/oRlnzbDOX8/","http://instagram.com/p/oRhIKzTArj/","http://instagram.com/p/oRgXqizAqT/","http://instagram.com/p/oRfvqaTApX/","http://instagram.com/p/oRfW15zAo3/","http://instagram.com/p/oQJORNhELU/","http://instagram.com/p/oOW4d-sGDJ/","http://instagram.com/p/oNo6zOi0cE/","http://instagram.com/p/oNaHH8ojgX/","http://instagram.com/p/oJoxnZmAwt/","http://instagram.com/p/oJhpvpnmXw/","http://instagram.com/p/oJYjLyyx9G/","http://instagram.com/p/oHJ_9hSO9C/","http://instagram.com/p/oG9qYCheEr/","http://instagram.com/p/oG6L_REnzY/","http://instagram.com/p/oG20Brqiao/","http://instagram.com/p/oGqd2Jwdxe/","http://instagram.com/p/oGl1QSA-c-/","http://instagram.com/p/oGlutslO7G/","http://instagram.com/p/oGWGQGA-QD/","http://instagram.com/p/oEF7MKkXs6/","http://instagram.com/p/oEHjDkEXvc/","http://instagram.com/p/oD7U4fGl-5/","http://instagram.com/p/oDoIUvo9X9/","http://instagram.com/p/oDXyMIxK_d/","http://instagram.com/p/oB38IPkUAj/","http://instagram.com/p/oBkvj5iDJh/","http://instagram.com/p/oBBeTCKzK1/","http://instagram.com/p/n_hjBVSKJr/","http://instagram.com/p/n_YgiJyKMb/","http://instagram.com/p/n_NKwcldYm/","http://instagram.com/p/n_De6WiJot/","http://instagram.com/p/n1JXY_hh-x/","http://instagram.com/p/n02RM8gd1S/","http://instagram.com/p/n0TeBWkenx/","http://instagram.com/p/nyNMiWtdg5/","http://instagram.com/p/nyI-oftdqj/","http://instagram.com/p/nyDR5WpOJe/","http://instagram.com/p/nx-FSKmi_O/","http://instagram.com/p/nxqUTMwa9h/","http://instagram.com/p/nwD19tkcL9/","http://instagram.com/p/ntNRMvJEGZ/","http://instagram.com/p/nvvacNnAJC/","http://instagram.com/p/nvvDKyHAIl/","http://instagram.com/p/nvdcD9llUO/","http://instagram.com/p/ntat-0RbJy/","http://instagram.com/p/nqBWIswMII/","http://instagram.com/p/npodN_Gf2J/","http://instagram.com/p/nnzA1em9ie/","http://instagram.com/p/nnu7BqwBrB/","http://instagram.com/p/nji0F3hKjW/","http://instagram.com/p/njaUk3JxFn/","http://instagram.com/p/niidbajz20/","http://instagram.com/p/niTrbSI-E1/","http://instagram.com/p/ngtdoajzzE/","http://instagram.com/p/ngtUunjzy4/","http://instagram.com/p/ngAFT3TFWU/","http://instagram.com/p/nd5k0kHANg/","http://instagram.com/p/ndhm_mk_0i/","http://instagram.com/p/nbLalrquzB/","http://instagram.com/p/nbEuripikX/","http://instagram.com/p/nbAx2ZJivY/","http://instagram.com/p/mvMzfSi3D9/","http://instagram.com/p/mtQPUXnVX5/","http://instagram.com/p/ms7yl8mKUP/","http://instagram.com/p/mp1C50QaxU/","http://instagram.com/p/mnDsbWMP9S/","http://instagram.com/p/mii4MTmE0X/","http://instagram.com/p/me9uQVhSz_/","http://instagram.com/p/mdF7BTED2t/","http://instagram.com/p/mcnnC-BXlo/","http://instagram.com/p/mYg_sCtVBv/","http://instagram.com/p/mTCt1IkUB8/","http://instagram.com/p/mS43icMEtT/","http://instagram.com/p/mQnMDXhpQB/","http://instagram.com/p/mQMQ95x9HQ/","http://instagram.com/p/mQEkhVwawR/","http://instagram.com/p/mNNNK_q50e/","http://instagram.com/p/mNLcC3IFrY/","http://instagram.com/p/mJEKAvA6jO/","http://instagram.com/p/mFoNBhtCMs/","http://instagram.com/p/mAETavQkuW/","http://instagram.com/p/l28EV0KVf7/","http://instagram.com/p/l9jPW9Gl9i/","http://instagram.com/p/l5SZN3tXHn/","http://instagram.com/p/l3dlETk0rz/","http://instagram.com/p/l2z5vmDsJu/","http://instagram.com/p/l2Gc4aEmuw/","http://instagram.com/p/lz20tOSltM/","http://instagram.com/p/lued99BtBK/","http://instagram.com/p/lnUmydwduD/","http://instagram.com/p/lnJJFOHb9B/","http://instagram.com/p/lnJJFOHb9B/","http://instagram.com/p/lktt4BAjvb/","http://instagram.com/p/lktiDfkMEn/","http://instagram.com/p/lj05xMCHub/","http://instagram.com/p/lh4sJiheDS/","http://instagram.com/p/lh0qP9E_2v/","http://instagram.com/p/le4GbEBvuw/","http://instagram.com/p/ldAjv7SeM4/","http://instagram.com/p/lag7T-LT3A/","http://instagram.com/p/laQyPmtCUH/","http://instagram.com/p/lXqmWyorWN/","http://instagram.com/p/lXlBvzlTDJ/","http://instagram.com/p/lSd4I5H_Lu/","http://instagram.com/p/lR4ki9uYIy/","http://instagram.com/p/lQOhFewnat/","http://instagram.com/p/lQDpJ4Sl-R/","http://instagram.com/p/lN4cPUj6KG/","http://instagram.com/p/lNWpeCu-4j/","http://instagram.com/p/lNWbcCQSCW/","http://instagram.com/p/lNVwCzxw8U/","http://instagram.com/p/lMzDlRE_0D/","http://instagram.com/p/lLHnu-v9wM/","http://instagram.com/p/lCzhrqCj4l/","http://instagram.com/p/lCcc--J-qm/","http://instagram.com/p/lBCcCgmqSt/","http://instagram.com/p/lA7NoEA56e/","http://instagram.com/p/lAuF1FuE41/","http://instagram.com/p/lAq7NNxojI/","http://instagram.com/p/lAkgYHOE4O/","http://instagram.com/p/k9906HihBX/","http://instagram.com/p/k7hpxhvSOX/","http://instagram.com/p/k7fCXxPSKn/","http://instagram.com/p/k7ZBbmPSCR/","http://instagram.com/p/k6fBMeKlLK/","http://instagram.com/p/k5Lhq9R9FX/","http://instagram.com/p/k4v89YiNU8/","http://instagram.com/p/k4qvZzCNeO/","http://instagram.com/p/kzTiqighaT/","http://instagram.com/p/kxLEhqwnZi/","http://instagram.com/p/kxHqbpROHD/","http://instagram.com/p/kxEhxFMP0k/","http://instagram.com/p/ktwo3EsZiG/","http://instagram.com/p/ksbufWFQ1A/","http://instagram.com/p/kpzTd0It31/","http://instagram.com/p/kpVwkFPKgR/","http://instagram.com/p/kpTdshp1Va/","http://instagram.com/p/kkbjJ2It5Y/","http://instagram.com/p/kkUTNPot-4/","http://instagram.com/p/kkGGINDz7F/","http://instagram.com/p/kjR9tNDz73/","http://instagram.com/p/kh7ph3rmHW/","http://instagram.com/p/kfNUqTKb-h/","http://instagram.com/p/ke0iK1IQ9g/","http://instagram.com/p/keqMp2xYxH/","http://instagram.com/p/kb7rngLB8m/","http://instagram.com/p/kbaAIknbEe/","http://instagram.com/p/kaH1BaHbGJ/","http://instagram.com/p/kZ6J2nDqFL/","http://instagram.com/p/kZ4hTsHGoY/","http://instagram.com/p/kXSqrjk_yX/","http://instagram.com/p/kXKXcOPifE/","http://instagram.com/p/kUto_rIRnP/","http://instagram.com/p/kRemcYvZkc/","http://instagram.com/p/kNdNwvk_9H/","http://instagram.com/p/kKoWhQM7yp/","http://instagram.com/p/kKk210M78K/","http://instagram.com/p/kKTiT4y7sg/","http://instagram.com/p/kKPWPfM74D/","http://instagram.com/p/kJ4GNJmavO/","http://instagram.com/p/kJnfThlFWk/","http://instagram.com/p/kH6vewHnno/","http://instagram.com/p/kC5HGrN-Il/","http://instagram.com/p/kCo9s_KZwx/","http://instagram.com/p/kCl-rJjN74/","http://instagram.com/p/kChLiZGmn4/","http://instagram.com/p/kBb-irypGL/","http://instagram.com/p/rxiwIiCNRb/","http://instagram.com/p/rxgawVCNdg/","http://instagram.com/p/rv54QPOa_j/","http://instagram.com/p/rv5xKpOa_c/","http://instagram.com/p/rnFFYoo7oN/","http://instagram.com/p/rfijDZFtGj/","http://instagram.com/p/rfXjoNltBj/","http://instagram.com/p/rc6Pu5R6_i/","http://instagram.com/p/rZRxz8KM4I/","http://instagram.com/p/rSfcipnj76/","http://instagram.com/p/rRDTxxJN84/","http://instagram.com/p/rM4JLLP83M/","http://instagram.com/p/rFrg7sNkal/","http://instagram.com/p/rFVfiFy3_z/","http://instagram.com/p/rC9AbgCPwy/","http://instagram.com/p/rCOF1HLNgn/","http://instagram.com/p/q9596eD_6L/","http://instagram.com/p/q7X-YKvF2T/","http://instagram.com/p/q7VK9_PFw1/","http://instagram.com/p/q6q9NrwQDC/","http://instagram.com/p/q4x1p7GA8H/","http://instagram.com/p/q4LDwwp7zg/","http://instagram.com/p/q2DcDqjhpA/","http://instagram.com/p/qzwx5oBjcW/","http://instagram.com/p/qzwx5oBjcW/","http://instagram.com/p/qw9hD5wjt8/","http://instagram.com/p/qsWg1fqMZB/","http://instagram.com/p/qpLFXBx1EF/","http://instagram.com/p/qovO0-DFz7/","http://instagram.com/p/qmX4FSL1Bd/","http://instagram.com/p/qjyiLClAM6/","http://instagram.com/p/qg7q2HAspD/","http://instagram.com/p/qSBo5cEnmz/","http://instagram.com/p/qL-cNwFMw2/","http://instagram.com/p/p_EArVhPXM/","http://instagram.com/p/p9Q3MgNpRh/","http://instagram.com/p/p9KihbtpXM/","http://instagram.com/p/p9GD9fPKqL/","http://instagram.com/p/p9F6CNvKp8/","http://instagram.com/p/pwV5S9yIjU/","http://instagram.com/p/pwS9XAo2wV/","http://instagram.com/p/pwHxnVu9Vl/","http://instagram.com/p/pq-83Gy2--/","http://instagram.com/p/pq9zewy29C/","http://instagram.com/p/pq9PvdS28P/","http://instagram.com/p/pq9MYfy28J/","http://instagram.com/p/popzj3K_Kw/","http://instagram.com/p/pmRTvQCfCS/","http://instagram.com/p/pjbSU0PWL6/","http://instagram.com/p/phC43mJuOM/","http://instagram.com/p/phA0m6JuJ2/","http://instagram.com/p/pgZiszsbUa/","http://instagram.com/p/pd1CQHx0ug/","http://instagram.com/p/pb63zvSoEB/","http://instagram.com/p/pZs9Toyh6V/","http://instagram.com/p/pZJhGVinhm/","http://instagram.com/p/pRqP20Mjzx/","http://instagram.com/p/pMcEworhkv/","http://instagram.com/p/pI0_CjCHH8/","http://instagram.com/p/pBE1fqvnLg/","http://instagram.com/p/o_XivKQlvI/","http://instagram.com/p/o9CR-qyUYZ/","http://instagram.com/p/o9CRx7yUYY/","http://instagram.com/p/o8rx11HKTK/","http://instagram.com/p/o6PjOmIqHr/","http://instagram.com/p/o50rj2wUUc/","http://instagram.com/p/o3ts0EuNhF/","http://instagram.com/p/oynCCCrTyi/","http://instagram.com/p/owNUdOmUlJ/","http://instagram.com/p/ovlFkQraw3/","http://instagram.com/p/ovlFkQraw3/","http://instagram.com/p/ovlFkQraw3/","http://instagram.com/p/ovkNkLCZIx/","http://instagram.com/p/ovWy7gmzbO/","http://instagram.com/p/otg0EFrT7U/","http://instagram.com/p/ormG_oLlty/","http://instagram.com/p/orFSh_KMlL/","http://instagram.com/p/orDcBlpWWt/","http://instagram.com/p/oq-OY3p07q/","http://instagram.com/p/oorMMBLhsi/","http://instagram.com/p/omAgXun0SC/","http://instagram.com/p/omAgXun0SC/","http://instagram.com/p/omAgXun0SC/","http://instagram.com/p/ovlFkQraw3/","http://instagram.com/p/oeIATmgqdY/","http://instagram.com/p/obgm8tsnpO/","http://instagram.com/p/obgefOBxFk/","http://instagram.com/p/oZjMp3Cpk2/","http://instagram.com/p/oZjFM6Cpkl/","http://instagram.com/p/oZW-21qBMZ/","http://instagram.com/p/oWFwQSiZGF/","http://instagram.com/p/oV7NzQraxX/","http://instagram.com/p/oV5OfviZDu/","http://instagram.com/p/oUFajTnVUu/","http://instagram.com/p/oRDV0mLblE/","http://instagram.com/p/oQ8ANMIAfj/","http://instagram.com/p/oOofiYtWpz/","http://instagram.com/p/oI67UYiHGB/","http://instagram.com/p/oHNJ03A2Nz/","http://instagram.com/p/oHIc7WzMoP/","http://instagram.com/p/oG0G__s8xu/","http://instagram.com/p/oG0G__s8xu/","http://instagram.com/p/oGmfxtQ4Hf/","http://instagram.com/p/oEX6DFokGX/","http://instagram.com/p/oEVjBaIkCm/","http://instagram.com/p/oEQN96okKf/","http://instagram.com/p/oEQAzBIkKA/","http://instagram.com/p/oEFtJfu9wm/","http://instagram.com/p/oDrZ7kCbgr/","http://instagram.com/p/oAzzIVO4dk/","http://instagram.com/p/oAdf3aquZy/","http://instagram.com/p/n_AmJ_JTZB/","http://instagram.com/p/n-ZJPjBBg0/","http://instagram.com/p/n1Iq08xDSP/","http://instagram.com/p/n0cZgymF87/","http://instagram.com/p/n0Jdv_t2DK/","http://instagram.com/p/nyLIfFi7-6/","http://instagram.com/p/nyLIfFi7-6/","http://instagram.com/p/nyLIfFi7-6/","http://instagram.com/p/nxh6giF4V_/","http://instagram.com/p/nxQBsBLK52/","http://instagram.com/p/nwbpZCNJgI/","http://instagram.com/p/nwVUILggOy/","http://instagram.com/p/numH65Mj50/","http://instagram.com/p/nqm8xgvqYI/","http://instagram.com/p/nk6AkUjKTD/","http://instagram.com/p/ni2R3juvAk/","http://instagram.com/p/nfhmR-COx6/","http://instagram.com/p/neOwvXqdGC/","http://instagram.com/p/neNHg-NsVV/","http://instagram.com/p/neLhswrfo0/","http://instagram.com/p/neKaYvKdAa/","http://instagram.com/p/mvNYm_D2pu/","http://instagram.com/p/mvNYm_D2pu/","http://instagram.com/p/muYqsrscBq/","http://instagram.com/p/mtBitvR-1l/","http://instagram.com/p/mrfS12hS1q/","http://instagram.com/p/mqkQ3zr9Gs/","http://instagram.com/p/mnFj68vWV4/","http://instagram.com/p/mcmD2wFrH_/","http://instagram.com/p/mckI3xsj7u/","http://instagram.com/p/mb_6y2H66T/","http://instagram.com/p/mbG3LagIUv/","http://instagram.com/p/ma9t2nSHYU/","http://instagram.com/p/mYb1C-nKVn/","http://instagram.com/p/mXyc7HS9Uf/","http://instagram.com/p/mV6cJKsj7y/","http://instagram.com/p/mLkct5GxAj/","http://instagram.com/p/mJI2sbMhZj/","http://instagram.com/p/mB6-nUTR9C/","http://instagram.com/p/mAumgOgeXC/","http://instagram.com/p/lzaGb7Mjwf/","http://instagram.com/p/ly50j-yUl0/","http://instagram.com/p/lwbKJvsj8Q/","http://instagram.com/p/lu9eFTP7Ld/","http://instagram.com/p/lu9eFTP7Ld/","http://instagram.com/p/lju8w_IQFq/","http://instagram.com/p/lju8w_IQFq/","http://instagram.com/p/lXxN5fpjoA/","http://instagram.com/p/lXPN2aBQFO/","http://instagram.com/p/lVggCsnlDY/","http://instagram.com/p/lVao4msj-i/","http://instagram.com/p/lUX6xGMjxh/","http://instagram.com/p/lS_V_XR5w9/","http://instagram.com/p/lS_V_XR5w9/","http://instagram.com/p/lS_V_XR5w9/","http://instagram.com/p/lSY_LrvWof/","http://instagram.com/p/lN2wb_ytWX/","http://instagram.com/p/kua8oCtBSp/","http://instagram.com/p/lLGdyBJhFl/","http://instagram.com/p/lBFrL6gheD/","http://instagram.com/p/lAj4BHyjsZ/","http://instagram.com/p/lANYnoiR21/","http://instagram.com/p/k-SKTME_y7/","http://instagram.com/p/k15hfOJV7O/","http://instagram.com/p/k0CdCWu9yl/","http://instagram.com/p/kxJVAmk_7P/","http://instagram.com/p/kw-csUB72G/","http://instagram.com/p/kuqk3mq55c/","http://instagram.com/p/kuTZNAlHs7/","http://instagram.com/p/ksnZtDrMIM/","http://instagram.com/p/ksRm73RtbZ/","http://instagram.com/p/knF5rxzRYQ/","http://instagram.com/p/km_E3mzRe0/","http://instagram.com/p/kkEFpKMj3j/","http://instagram.com/p/kjgFroMj9t/","http://instagram.com/p/kgpaRUsjwS/","http://instagram.com/p/kfMH9kGA72/","http://instagram.com/p/ke3hv0uVmM/","http://instagram.com/p/kekk9ThVj5/","http://instagram.com/p/kcosWfpuR-/","http://instagram.com/p/kcSf3QDzzk/","http://instagram.com/p/kbQSGqI-SC/","http://instagram.com/p/kZpccWO1pR/","http://instagram.com/p/kKJU6XE6S6/","http://instagram.com/p/kJ9PcFMj8h/","http://instagram.com/p/kAv7VcTNbq/");

//https://api.instagram.com/v1/media/shortcode/rwV2Bslogc

$len = count($links);

$token = '1543782233.1fb234f.9fa10e5a4a08443186b264ae3d127214';

$out = "";

for($i=0; $i<$len; $i++){
	preg_match_all('/http:\/\/instagram.com\/p\/(?<shortcode>[a-z0-9\-]+)/isu', $links[$i], $shortcode);
	/*echo $i." ";
	print_r($shortcode['shortcode'][0]);
	echo "\n";*/
	$cont=parseURL("https://api.instagram.com/v1/media/shortcode/".$shortcode['shortcode'][0]."?access_token=".$token);
	unset($shortcode);
	$mcont = json_decode($cont, true);
	//print_r($mcont);
	$comments = 0;
	$likes = 0;
	$id = "";
	$followed_by = 0;
	$username = "";
	$fullname = "";

	if($mcont['meta']['code']==200){
		$comments = $mcont['data']['comments']['count'];
		$likes = $mcont['data']['likes']['count'];
		$id = $mcont['data']['user']['id'];
		//echo $id." ".$likes." ".$comments."\n";
		echo $i."\n";
		// print_r($mcont['data']);
		if($id!=""){
			//sleep(1);
			//echo "get info\n";
			$acont = parseURL("https://api.instagram.com/v1/users/".$id."?access_token=".$token);
			$ucont = json_decode($acont, true);
			//print_r($acont);
			$followed_by = $ucont['data']['counts']['followed_by'];
			$username = $ucont['data']['username'];
			$fullname = $ucont['data']['full_name'];
		}
		//$out.=$id."\t".$likes."\t".$comments."\t".$followed_by."\n";
		$out.=$username."\t".$fullname."\t".$followed_by."\t".($likes+$comments)."\n";
	} else {
		echo "error\n";
		$out.="error\n";
	}
}

file_put_contents('result.txt', $out);

function get_tag_instagram($grid,$ts,$te)
{
	$token = '1543782233.1fb234f.9fa10e5a4a08443186b264ae3d127214';

	$outmas = array();
	$next_link = '';
	$iter = 0;
	$count = "&count=50";
	$rus = 0;

	do{
	    $iter++;
	    if($next_link!=''){
	        $url = $next_link;
	    } else {
	        $url = 'https://api.instagram.com/v1/tags/'.$grid.'/media/recent?access_token='.$token;
	   

	    }

	    $cont=parseURL($url.$count);

	    $mcont = json_decode($cont, true);

	    $next_link = $mcont['pagination']['next_url'];
	    $data = $mcont['data'];
	    echo count($data);
	    echo "\n";
	    foreach ($data as $key => $value) {
	    	$time = $data[$key]['caption']['created_time'];
	    	if ($time<$ts || $time>($te+86400)) continue;
	        $outmas['link'][] = $data[$key]['link'];
	        $outmas['content'][] = $data[$key]['caption']['text'];
	        $outmas['time'][] = $time;
	        $outmas['engage'][]=0;
			$outmas['adv_engage'][]='';
			$outmas['author_id'][]='';
	    }

	    if($time<$ts) break;
	    if($iter>100) break; 
	} while(isset($mcont['pagination']['next_url']));
	return $outmas;
}



//get_tag_instagram('музеон',mktime(0,0,0,10,30,2014),mktime(0,0,0,10,31,2014));

?>
