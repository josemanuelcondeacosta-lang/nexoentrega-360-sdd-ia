const {test,expect}=require('@playwright/test');
async function login(page,role){
 await page.goto('/');await page.getByLabel('Correo electrónico').fill(`org-a-${role}@example.test`);await page.getByLabel('Contraseña',{exact:true}).fill('DemoNexo360!');
 await page.getByRole('button',{name:'Iniciar sesión'}).click();await expect(page.getByRole('button',{name:'Cerrar sesión'})).toBeVisible();
}
async function logout(page){await page.getByRole('button',{name:'Cerrar sesión'}).click();await expect(page.getByLabel('Correo electrónico')).toBeVisible();}
test('flujo por cuatro roles, inventario y auditoría',async({page})=>{
 await login(page,'solicitante');await page.getByRole('button',{name:'Nueva solicitud'}).click();
 await page.getByRole('spinbutton',{name:'Cantidad'}).fill('1');const created=page.waitForResponse(r=>r.url().endsWith('/api/requests')&&r.request().method()==='POST');await page.getByRole('button',{name:'Guardar borrador'}).click();const id=(await (await created).json()).id;
 await expect(page.getByRole('dialog')).not.toBeVisible();
 const card=page.locator(`[data-request="${id}"]`);const code=(await card.locator('h3').textContent()).split(' ')[0];
 await card.getByRole('button',{name:'Enviar',exact:true}).click();await expect(card.locator('.badge')).toHaveText('Enviada');await logout(page);
 await login(page,'coordinador');let request=page.locator('.request-card').filter({hasText:code});await request.getByRole('button',{name:'Reservar',exact:true}).click();await expect(request.locator('.badge')).toHaveText('Reservada');await logout(page);
 await login(page,'bodega');request=page.locator('.request-card').filter({hasText:code});await request.getByRole('button',{name:'Despachar',exact:true}).click();await expect(request.locator('.badge')).toHaveText('Despachada');await logout(page);
 await login(page,'coordinador');request=page.locator('.request-card').filter({hasText:code});await request.getByRole('button',{name:'Registrar entrega'}).click();await expect(request.locator('.badge')).toHaveText('Entregada');await logout(page);
 await login(page,'auditor');await page.getByRole('button',{name:'Auditoría',exact:true}).click();await expect(page.locator('.audit-event').filter({hasText:'DELIVER'}).first()).toBeVisible();
 expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBeTruthy();
 await page.screenshot({path:`test-results/flujo-${test.info().project.name}.png`,fullPage:true});
});
test('teclado, validación y rechazo de reserva sin efectos',async({page})=>{
 await page.goto('/');await page.getByLabel('Correo electrónico').focus();await page.keyboard.type('org-a-solicitante@example.test');await page.keyboard.press('Tab');await expect(page.getByLabel('Contraseña',{exact:true})).toBeFocused();await page.keyboard.type('DemoNexo360!');await page.keyboard.press('Enter');
 await expect(page.getByRole('button',{name:'Nueva solicitud'})).toBeVisible();await page.getByRole('button',{name:'Nueva solicitud'}).click();await expect(page.getByRole('dialog').getByLabel('Zona',{exact:true})).toBeFocused();
 await page.getByRole('spinbutton',{name:'Cantidad'}).fill('500');const created=page.waitForResponse(r=>r.url().endsWith('/api/requests')&&r.request().method()==='POST');await page.getByRole('button',{name:'Guardar borrador'}).click();const id=(await (await created).json()).id;await expect(page.getByRole('dialog')).not.toBeVisible();
 let card=page.locator(`[data-request="${id}"]`);await card.getByRole('button',{name:'Enviar',exact:true}).click();await expect(card.locator('.badge')).toHaveText('Enviada');await logout(page);
 await login(page,'coordinador');const inventoryLoaded=page.waitForResponse(r=>r.url().includes('/api/inventory?'));await page.getByRole('button',{name:'Inventario',exact:true}).click();await inventoryLoaded;await expect(page.locator('.inventory-card')).toHaveCount(3);const before=await page.locator('#inventory-list').textContent();await page.getByRole('button',{name:/^Solicitudes/}).click();
 card=page.locator(`[data-request="${id}"]`);await card.getByRole('button',{name:'Reservar',exact:true}).click();await expect(page.getByRole('status')).toContainText('Inventario insuficiente');await expect(card.locator('.badge')).toHaveText('Enviada');
 await page.getByRole('button',{name:'Inventario',exact:true}).click();await expect(page.locator('#inventory-list')).toHaveText(before);
 expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBeTruthy();
 const csrf=await page.evaluate(async()=>{const r=await fetch('/api/ai/summaries',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({requestIds:[]})});return r.status;});expect(csrf).toBe(419);
});
