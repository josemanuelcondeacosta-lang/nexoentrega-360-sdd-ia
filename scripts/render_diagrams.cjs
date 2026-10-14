const fs = require('node:fs');
const {chromium} = require('@playwright/test');
(async () => {
    const config = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
    const browser = await chromium.launch();
    try {
        const page = await browser.newPage({viewport: {width: 1800, height: 1200}, deviceScaleFactor: 2});
        page.setDefaultTimeout(30000);
        await page.setContent('<html><body style="margin:20px;background:white"></body></html>');
        await page.addScriptTag({path: config.bundle});
        await page.evaluate(() => mermaid.initialize({startOnLoad: false, securityLevel: 'strict', theme: 'neutral', themeVariables: {fontFamily: 'Arial', fontSize: '17px'}, flowchart: {useMaxWidth: false}, sequence: {useMaxWidth: false}, er: {useMaxWidth: false}}));
        for (let i = 0; i < config.jobs.length; i++) {
            const job = config.jobs[i];
            const source = fs.readFileSync(job.input, 'utf8');
            await page.evaluate(async ({source, i}) => {
                const result = await Promise.race([mermaid.render('diagram' + i, source), new Promise((_, reject) => setTimeout(() => reject(Error('Render timeout')), 30000))]);
                document.body.innerHTML = result.svg;
            }, {source, i});
            await page.locator('svg').first().evaluate(svg => {
                const box = svg.viewBox.baseVal;
                svg.style.maxWidth = 'none'; svg.style.width = Math.ceil(box.width) + 'px'; svg.style.height = Math.ceil(box.height) + 'px';
            });
            await page.locator('svg').first().screenshot({path: job.output, timeout: 30000});
            console.log('Rendered ' + job.output.split(/[\\/]/).pop());
        }
    } finally { await browser.close(); }
})().catch(error => { console.error(error.message); process.exit(1); });
