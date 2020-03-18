import unittest
import sys

class Test2(unittest.TestCase):
    def test_deepbugs(self):
        from volatility.main import parse
        with open('tests/django-rest-auth.txt', 'r') as f:
            content = f.read()
            res = parse(content)
            self.assertEqual(res['commits'], 517)


if __name__ == '__main__':
    unittest.main()
