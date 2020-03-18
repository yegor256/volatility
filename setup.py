import setuptools

with open("README.md", "r") as fh:
    long_description = fh.read()

setuptools.setup(
    name="volatility-zuoqin",
    version="0.0.1",
    author="Alexey Zorchenkov",
    author_email="zorchenkov.alexey@huawei.com",
    description="Calculating git repository volatility",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/yegor256/volatility",
    packages=setuptools.find_packages(),
    scripts=['bin/volatility'],
    classifiers=[
        "Programming Language :: Python :: 3",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
    ],
    python_requires='>=3.6',
)
