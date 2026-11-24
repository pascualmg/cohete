import sys, asyncio
from playwright.async_api import async_playwright
OUT = sys.argv[1]; URL = "https://pascualmg.dev/selena"
async def go(pg, sel, off=0):
    await pg.evaluate(f"(() => {{ const el = document.querySelector('{sel}'); window.scrollTo(0, el.getBoundingClientRect().top + scrollY + {off}); }})()")
    await pg.wait_for_timeout(1800)
async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        d = await (await b.new_context(viewport={"width":1440,"height":900}, device_scale_factor=1.5, reduced_motion="reduce")).new_page()
        await d.goto(URL, wait_until="networkidle"); await d.wait_for_timeout(1500)
        await d.screenshot(path=f"{OUT}/d-hero.png")
        await go(d, "#s01"); await d.screenshot(path=f"{OUT}/d-s01.png")
        await go(d, "#s03", 330); await d.screenshot(path=f"{OUT}/d-s03.png")
        m = await (await b.new_context(viewport={"width":390,"height":844}, device_scale_factor=2, is_mobile=True, has_touch=True, reduced_motion="reduce")).new_page()
        await m.goto(URL, wait_until="networkidle"); await m.wait_for_timeout(1500)
        await m.screenshot(path=f"{OUT}/m-hero.png")
        await go(m, "#s01", 560); await m.screenshot(path=f"{OUT}/m-s01.png")
        await go(m, "#s02", 640); await m.screenshot(path=f"{OUT}/m-s02.png")
        await go(m, "#contacto"); await m.screenshot(path=f"{OUT}/m-contacto.png")
        await b.close()
asyncio.run(main())
