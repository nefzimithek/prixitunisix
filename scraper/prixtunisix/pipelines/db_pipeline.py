"""
DbPipeline — writes scraped offers directly to PostgreSQL.
Also appends a price_history row for every price change.
"""
import psycopg2
from itemadapter import ItemAdapter
from scrapy import Spider
from scrapy.exceptions import DropItem

from prixtunisix.settings import DB_HOST, DB_NAME, DB_PASS, DB_PORT, DB_USER


class DbPipeline:
    def open_spider(self, spider: Spider):
        self.conn = psycopg2.connect(
            host=DB_HOST,
            port=DB_PORT,
            dbname=DB_NAME,
            user=DB_USER,
            password=DB_PASS,
        )
        self.cur = self.conn.cursor()

    def close_spider(self, spider: Spider):
        self.conn.commit()
        self.cur.close()
        self.conn.close()

    def process_item(self, item, spider: Spider):
        adapter = ItemAdapter(item)

        raw_title   = adapter.get("raw_title", "").strip()
        price       = adapter.get("price", 0)
        merchant_url = adapter.get("merchant_url", "")
        image_url   = adapter.get("image_url")
        is_available = bool(adapter.get("is_available", True))
        website_id  = adapter.get("merchant_website_id")
        scraped_at  = adapter.get("scraped_at")

        if not raw_title or not merchant_url:
            raise DropItem(f"Missing title or URL: {item}")

        # Upsert offer by merchant_url (unique per merchant site)
        self.cur.execute(
            """
            INSERT INTO offers
                (merchant_website_id, raw_title, price, is_available,
                 merchant_url, image_url, scraped_at, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
            ON CONFLICT (merchant_url) DO UPDATE SET
                price        = EXCLUDED.price,
                is_available = EXCLUDED.is_available,
                image_url    = EXCLUDED.image_url,
                scraped_at   = EXCLUDED.scraped_at,
                updated_at   = NOW()
            RETURNING id, (xmax = 0) AS inserted
            """,
            (website_id, raw_title, price, is_available,
             merchant_url, image_url, scraped_at),
        )
        row = self.cur.fetchone()
        offer_id, was_inserted = row

        # Always append a price_history row
        self.cur.execute(
            """
            INSERT INTO price_history (offer_id, price, recorded_at)
            VALUES (%s, %s, %s)
            """,
            (offer_id, price, scraped_at),
        )
        self.conn.commit()
        return item
