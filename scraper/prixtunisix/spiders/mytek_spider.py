"""
MyTek spider — scrapes product listings from mytek.tn.

NOTE: This spider is structured to be polite (DOWNLOAD_DELAY=1,
AUTOTHROTTLE enabled). Only use on pages you are allowed to crawl
per the site's terms of service and robots.txt.
"""
import re
from datetime import datetime, timezone

import scrapy

from prixtunisix.items import OfferItem

# merchant_website.id for MyTek in the DB (populated by seeder)
MYTEK_WEBSITE_ID = 1


class MytekSpider(scrapy.Spider):
    name = "mytek"
    allowed_domains = ["www.mytek.tn"]
    start_urls = [
        "https://www.mytek.tn/pc-portable.html",
        "https://www.mytek.tn/smartphones.html",
    ]

    def parse(self, response):
        # Product cards on listing pages
        for card in response.css("li.item.product.product-item"):
            yield self._parse_card(card)

        # Follow pagination
        next_page = response.css("a.action.next::attr(href)").get()
        if next_page:
            yield response.follow(next_page, self.parse)

    def _parse_card(self, card) -> OfferItem:
        raw_price = card.css("span.price::text").get("0").strip()
        price = float(re.sub(r"[^\d,.]", "", raw_price).replace(",", ".") or 0)

        return OfferItem(
            raw_title=card.css("a.product-item-link::text").get("").strip(),
            price=price,
            merchant_url=card.css("a.product-item-link::attr(href)").get(""),
            image_url=card.css("img.product-image-photo::attr(src)").get(),
            is_available="out-of-stock" not in card.attrib.get("class", ""),
            merchant_website_id=MYTEK_WEBSITE_ID,
            scraped_at=datetime.now(timezone.utc).isoformat(),
        )
