const {defineConfig}=require('@playwright/test');
module.exports=defineConfig({testDir:'./tests/e2e',workers:1,fullyParallel:false,timeout:120000,expect:{timeout:15000},
  reporter:[['list'],['html',{open:'never'}],['json',{outputFile:'test-results/results.json'}]],
  use:{baseURL:process.env.E2E_BASE_URL||'http://127.0.0.1:8000',...(process.env.E2E_CHANNEL?{channel:process.env.E2E_CHANNEL}:{}),trace:'retain-on-failure',screenshot:'only-on-failure'},
  projects:[{name:'desktop',use:{viewport:{width:1366,height:900}}},{name:'mobile',use:{viewport:{width:390,height:844}}}]
});
